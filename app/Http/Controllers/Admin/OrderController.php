<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function index(Request $request)
    {
        $orders = Order::with(['user', 'payment'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('order_number', 'like', '%'.$request->search.'%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        foreach ($orders as $order) {
            if ($order->status === 'menunggu_pembayaran' && $order->payment?->payment_method === 'midtrans') {
                $this->payments->syncWithMidtrans($order->payment);
                $order->refresh();
            }
        }

        $statuses = Order::STATUSES;

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items', 'payment']);

        if ($order->status === 'menunggu_pembayaran' && $order->payment?->payment_method === 'midtrans') {
            $this->payments->syncWithMidtrans($order->payment);
            $order->refresh();
        }

        return view('admin.orders.show', compact('order'));
    }

    public function verifyPayment(Order $order)
    {
        $order->loadMissing('payment');

        if (! $order->payment) {
            return back()->with('error', 'Pesanan ini tidak memiliki invoice pembayaran.');
        }

        if ($order->payment->isPaid()) {
            return back()->with('error', "Pembayaran {$order->payment->invoice_number} sudah ditandai lunas.");
        }

        if ($order->payment->payment_method === 'midtrans') {
            $payment = $this->payments->syncWithMidtrans($order->payment);
            $order->refresh();

            if ($payment->isPaid()) {
                return back()->with('success', "Status Midtrans berhasil disinkronkan: LUNAS ({$payment->transaction_status}). Pesanan otomatis diubah ke 'Diproses'.");
            }

            return back()->with('error', 'Status Midtrans: '.($payment->transaction_status ?? 'belum ada pembayaran').'. Pembayaran belum terkonfirmasi oleh Midtrans.');
        }

        $this->payments->markAsPaid($order->payment);

        return back()->with('success', "Pembayaran {$order->payment->invoice_number} telah diverifikasi.");
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:menunggu_pembayaran,diproses,dikirim,selesai,dibatalkan',
        ]);

        $order->loadMissing('payment');

        if ($order->payment_method === 'midtrans' && $order->payment_status !== 'paid' && in_array($request->status, ['diproses', 'dikirim', 'selesai'], true)) {
            return back()->with('error', 'Pesanan dengan metode Midtrans tidak dapat diproses atau diselesaikan sebelum pembayaran berhasil diverifikasi.');
        }

        $order->update(['status' => $request->status]);

        if ($request->status === 'selesai' && $order->payment_method === 'cod') {
            $order->update(['payment_status' => 'paid']);
            if ($order->payment && ! $order->payment->isPaid()) {
                $this->payments->markAsPaid($order->payment);
            }
        }

        return back()->with('success', "Status pesanan {$order->order_number} diubah ke \""
            .Order::statusLabel($request->status).'".');
    }

    public function destroy(Order $order)
    {
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

        $order->delete();

        return back()->with('success', 'Pesanan dihapus.');
    }
}
