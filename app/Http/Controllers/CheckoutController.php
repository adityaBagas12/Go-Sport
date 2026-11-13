<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function index(Request $request)
    {
        $selected = $this->resolveSelection($request);

        if ($selected->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Silakan pilih minimal satu produk untuk checkout.');
        }

        $request->session()->put('checkout_selection', $selected->modelKeys());

        $items = $request->user()->cartItems()
            ->with('product.category')
            ->whereIn('id', $selected->modelKeys())
            ->get();

        $subtotal = $items->sum('subtotal');
        $code = $request->session()->get('cart_coupon');
        $coupon = $code ? Coupon::where('code', $code)->where('is_active', true)->first() : null;
        $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0;

        $addresses = $request->user()->addresses()->latest()->get();
        $shippingOptions = config('gosport.shipping');
        $shippingRegions = config('gosport.shipping_regions');
        $paymentMethods = config('gosport.payment_methods');

        return view('checkout.index', compact(
            'items', 'subtotal', 'addresses', 'shippingOptions', 'shippingRegions', 'paymentMethods', 'discount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'full_address' => 'required|string|max:1000',
            'courier' => 'required|string',
            'service' => 'required|string',
            'payment_method' => ['required', 'string', Rule::in(array_keys(config('gosport.payment_methods')))],
            'note' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        $ids = array_map('intval', (array) $request->input('items', []));
        $ids = empty($ids)
            ? (array) $request->session()->get('checkout_selection', [])
            : $ids;

        $items = empty($ids)
            ? collect()
            : $user->cartItems()->with('product')->whereIn('id', $ids)->get();

        if ($items->isEmpty()) {
            $request->session()->forget('checkout_selection');

            return redirect()->route('cart.index')
                ->with('error', 'Silakan pilih minimal satu produk untuk checkout.');
        }

        $couriers = collect(config('gosport.shipping'));
        $courier = $couriers->firstWhere('code', $request->courier);

        if (! $courier || ! array_key_exists($request->service, $courier['services'])) {
            return back()->with('error', 'Jasa pengiriman tidak valid.');
        }

        $shippingRegion = $this->shippingRegion($request->province);

        if (! $shippingRegion) {
            return back()
                ->withErrors(['province' => 'Provinsi pengiriman tidak ditemukan.'])
                ->withInput();
        }

        $shortage = $this->findStockShortage($items);
        if ($shortage) {
            return back()->with('error', $shortage);
        }

        $subtotal = $items->sum(fn ($item) => $item->product->price * $item->quantity);
        $code = $request->session()->get('cart_coupon');
        $coupon = $code ? Coupon::where('code', $code)->where('is_active', true)->first() : null;
        $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0;

        $shippingFee = $courier['services'][$request->service] + $shippingRegion['surcharge'];
        $total = max($subtotal - $discount, 0) + $shippingFee;

        $payment = DB::transaction(function () use ($request, $user, $items, $subtotal, $discount, $shippingFee, $total, $courier) {
            $order = $user->orders()->create([
                'order_number' => $this->generateOrderNumber(),
                'status' => 'menunggu_pembayaran',
                'courier' => $courier['name'],
                'shipping_service' => $request->service,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'postal_code' => $request->postal_code,
                'full_address' => $request->full_address,
                'note' => $request->note,
            ]);

            foreach ($items as $item) {
                $item->product->decrement('stock', $item->quantity);
                $item->product->increment('sold', $item->quantity);

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_image' => $item->product->image,
                    'size' => $item->size,
                    'color' => $item->color,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'subtotal' => $item->product->price * $item->quantity,
                ]);
            }

            $user->cartItems()->whereIn('id', $items->modelKeys())->delete();

            return $this->payments->createForOrder($order);
        });

        $request->session()->forget('cart_coupon');
        $request->session()->forget('checkout_note');
        $request->session()->forget('checkout_selection');

        return redirect()->route('payment.invoice', $payment->invoice_number)
            ->with('success', 'Pesanan berhasil dibuat. Selesaikan pembayaran melalui invoice #'.$payment->invoice_number.'.');
    }

    /**
     * Cart item milik user yang benar-benar dipilih user.
     *
     * @return Collection<int, CartItem>
     */
    private function resolveSelection(Request $request): Collection
    {
        $raw = (array) $request->input('items', []);

        if (in_array('__all__', $raw, true)) {
            return $request->user()->cartItems()->get();
        }

        $ids = array_filter(array_map('intval', $raw));

        if (empty($ids)) {
            return collect();
        }

        return $request->user()->cartItems()
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * Pesan error pertama jika ada produk yang stoknya tidak mencukupi.
     */
    private function findStockShortage(Collection $items): ?string
    {
        foreach ($items as $item) {
            if ($item->quantity > $item->product->stock) {
                return "Stok \"{$item->product->name}\" tidak mencukupi. Tersisa {$item->product->stock}, dipesan {$item->quantity}.";
            }
        }

        return null;
    }

    /**
     * @return array{name: string, surcharge: int, provinces: array<int, string>}|null
     */
    private function shippingRegion(string $province): ?array
    {
        $province = Str::lower(trim($province));

        return collect(config('gosport.shipping_regions'))
            ->first(fn (array $region): bool => in_array($province, array_map(Str::lower(...), $region['provinces']), true));
    }

    private function generateOrderNumber(): string
    {
        return 'GS-'.now()->format('ymdHis').Str::upper(Str::random(4));
    }
}
