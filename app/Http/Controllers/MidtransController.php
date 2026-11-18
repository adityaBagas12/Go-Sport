<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Endpoint yang berkomunikasi dengan Midtrans setelah customer membayar.
 */
class MidtransController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
        private PaymentService $payments,
    ) {}

    /**
     * Notification/webhook dari Midtrans.
     *
     * Ini satu-satunya tempat status pembayaran boleh menjadi `paid`.
     * Route ini berada di luar middleware `auth` karena dipanggil langsung
     * oleh server Midtrans, dan tidak memakai CSRF karena bukan browser.
     *
     * Isi request tidak dipercaya mentah-mentah: `Midtrans\Notification`
     * melakukan request balik ke API Midtrans berdasarkan `transaction_id`,
     * sehingga payload yang diproses sudah terverifikasi oleh penyedia.
     */
    public function notification(Request $request): JsonResponse
    {
        try {
            $payload = $this->midtrans->notificationPayload();
        } catch (Throwable $exception) {
            Log::warning('Notification Midtrans gagal diverifikasi.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Notification could not be verified.'], 403);
        }

        try {
            $result = $this->payments->handleMidtransNotification($payload);
        } catch (Throwable $exception) {
            Log::error('Gagal memproses notification Midtrans.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Notification could not be processed.'], 500);
        }

        $payment = $result['payment'];

        if (! $payment) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'invoice_number' => $payment->invoice_number,
            'transaction_status' => $payment->transaction_status,
            'payment_status' => $payment->payment_status,
        ]);
    }

    /**
     * Customer menyelesaikan pembayaran di Midtrans.
     *
     * Halaman ini TIDAK pernah mengubah status menjadi lunas. Status final
     * hanya berasal dari notification. Halaman ini hanya mengarahkan
     * customer ke invoice, yang menampilkan status terbaru dari database.
     */
    public function finish(Request $request)
    {
        $order = $this->findInvoiceFromCallback($request);

        if ($order && $order->payment && ! $order->payment->isPaid()) {
            $this->payments->syncWithMidtrans($order->payment);
            $order->refresh();
        }

        $isPaid = $order?->payment?->isPaid();

        return view('midtrans.callback', [
            'tone' => $isPaid ? 'success' : 'info',
            'title' => $isPaid ? 'Pembayaran Berhasil' : 'Pembayaran dikirim',
            'message' => $isPaid
                ? 'Pembayaran Anda telah berhasil diverifikasi oleh Midtrans. Pesanan Anda saat ini sedang diproses.'
                : 'Pembayaran Anda sudah dikirim ke Midtrans. Status invoice akan diperbarui otomatis setelah Midtrans mengonfirmasi pembayaran.',
            'invoice' => $order,
        ]);
    }

    /**
     * Customer menutup halaman Midtrans tanpa menyelesaikan pembayaran.
     */
    public function unfinish(Request $request)
    {
        return view('midtrans.callback', [
            'tone' => 'warning',
            'title' => 'Pembayaran belum selesai',
            'message' => 'Anda belum menyelesaikan pembayaran. Invoice masih menunggu pembayaran dan bisa dibayar kembali selama masa berlaku transaksi.',
            'invoice' => $this->findInvoiceFromCallback($request),
        ]);
    }

    /**
     * Terjadi kendala atau pembayaran gagal di halaman Midtrans.
     */
    public function error(Request $request)
    {
        return view('midtrans.callback', [
            'tone' => 'danger',
            'title' => 'Pembayaran bermasalah',
            'message' => 'Pembayaran tidak dapat diproses. Silakan coba lagi atau hubungi tim GoSport jika kendala berlanjut.',
            'invoice' => $this->findInvoiceFromCallback($request),
        ]);
    }

    /**
     * Midtrans menyertakan `order_id` pada URL callback.
     *
     * Invoice yang ditampilkan hanya milik customer yang sedang login, dan
     * hanya untuk memshortcut navigasi — bukan untuk menentukan status.
     */
    private function findInvoiceFromCallback(Request $request): ?Order
    {
        $orderId = (string) $request->query('order_id', '');

        if ($orderId === '') {
            return null;
        }

        if ($request->user()) {
            $order = $request->user()->orders()->where('order_number', $orderId)->first();
            if ($order) {
                return $order;
            }
        }

        return Order::where('order_number', $orderId)->first();
    }
}
