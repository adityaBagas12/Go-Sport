<?php

namespace App\Http\Controllers;

use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Request $request, string $orderNumber)
    {
        $order = $request->user()->orders()
            ->with(['items', 'user', 'payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->status === 'menunggu_pembayaran' && $order->payment?->payment_method === 'midtrans') {
            app(PaymentService::class)->syncWithMidtrans($order->payment);
            $order->refresh();
        }

        return view('account.order-show', compact('order'));
    }

    public function cancel(Request $request, string $orderNumber)
    {
        $order = $request->user()->orders()->where('order_number', $orderNumber)->firstOrFail();

        if ($order->status !== 'menunggu_pembayaran') {
            return back()->with('error', 'Pesanan hanya bisa dibatalkan saat menunggu pembayaran.');
        }

        foreach ($order->items as $item) {
            if ($item->product) {
                if ($item->product->hasVariants()) {
                    $variant = $item->product->findVariant($item->size, $item->color);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                    }
                }

                $item->product->increment('stock', $item->quantity);
                $item->product->decrement('sold', $item->quantity);
            }
        }

        $order->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Pesanan dibatalkan.');
    }
}
