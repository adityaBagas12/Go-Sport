<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function coupon(Request $request): ?Coupon
    {
        $code = $request->session()->get('cart_coupon');

        if (! $code) {
            return null;
        }

        return Coupon::where('code', $code)->where('is_active', true)->first();
    }

    public function index(Request $request)
    {
        $items = Auth::user()->cartItems()->with(['product.category'])->get();
        $subtotal = $items->sum('subtotal');
        $coupon = $this->coupon($request);
        $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0;

        return view('cart.index', compact('items', 'subtotal', 'coupon', 'discount'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return back()->with('error', "Stok \"{$product->name}\" saat ini habis.");
        }

        $cartItem = Auth::user()->cartItems()
            ->where('product_id', $product->id)
            ->where('size', $request->size ?? null)
            ->where('color', $request->color ?? null)
            ->first();

        $currentQty = $cartItem ? $cartItem->quantity : 0;
        $totalQty = $currentQty + (int) $request->quantity;

        if ($totalQty > $product->stock) {
            return back()->with('error', "Jumlah melebihi stok yang tersedia. Stok tersisa {$product->stock} unit".($currentQty > 0 ? " (sudah ada {$currentQty} di keranjang)." : '.'));
        }

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            $cartItem = Auth::user()->cartItems()->create([
                'product_id' => $product->id,
                'size' => $request->size,
                'color' => $request->color,
                'quantity' => $request->quantity,
            ]);
        }

        $request->session()->flash('success', 'Produk berhasil ditambahkan ke keranjang.');

        if ($request->input('redirect') === 'cart') {
            return redirect()->route('cart.index');
        }

        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.index', ['items' => [$cartItem->id]]);
        }

        return back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        foreach ($request->quantity as $id => $quantity) {
            $item = $user->cartItems()->with('product')->findOrFail($id);

            if ($quantity > $item->product->stock) {
                return back()->with('error', "Stok \"{$item->product->name}\" tidak mencukupi. Tersisa {$item->product->stock} unit.");
            }
        }

        foreach ($request->quantity as $id => $quantity) {
            $user->cartItems()->whereKey($id)->update(['quantity' => $quantity]);
        }

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function remove($id)
    {
        Auth::user()->cartItems()->where('id', $id)->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:30']);

        $code = strtoupper($request->code);
        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (! $coupon) {
            return back()->with('error', 'Kode voucher tidak valid.');
        }

        $subtotal = Auth::user()->cartItems()->with('product')->get()->sum('subtotal');

        if ($subtotal < $coupon->min_order) {
            return back()->with(
                'error',
                'Voucher membutuhkan minimal belanja Rp '.number_format($coupon->min_order, 0, ',', '.')
            );
        }

        $request->session()->put('cart_coupon', $coupon->code);

        return back()->with('success', "Voucher \"{$coupon->code}\" berhasil digunakan.");
    }

    public function removeCoupon(Request $request)
    {
        $request->session()->forget('cart_coupon');

        return back()->with('success', 'Voucher dihapus.');
    }
}
