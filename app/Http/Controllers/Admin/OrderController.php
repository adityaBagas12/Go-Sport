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
        $orders = Order::with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('order_number', 'like', '%'.$request->search.'%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statuses = Order::STATUSES;

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items', 'payment']);

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
            return back()->with('error', 'Status pembayaran Midtrans hanya dapat diverifikasi otomatis melalui webhook notifikasi resmi Midtrans.');
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
                $item->product->increment('stock', $item->quantity);
                $item->product->decrement('sold', $item->quantity);
            }
        }

        $order->delete();

        return back()->with('success', 'Pesanan dihapus.');
    }
}
