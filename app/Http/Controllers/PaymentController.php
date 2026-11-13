<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\MidtransService;
use App\Services\Payment\PaymentException;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $payments,
        private MidtransService $midtrans,
    ) {}

    /**
     * Halaman invoice / status pembayaran.
     */
    public function show(Request $request, string $invoiceNumber)
    {
        $payment = $this->findOwnedPayment($request, $invoiceNumber, true);

        return view('invoice.show', compact('payment'));
    }

    /**
     * Customer menekan "Bayar Sekarang".
     *
     * Transaksi Snap dibuat di sini (bukan saat checkout) agar pesanan yang
     * ditinggalkan tidak menghasilkan transaksi kosong di Midtrans. Setelah
     * token tersedia, customer langsung diarahkan ke halaman pembayaran
     * Midtrans untuk memilih metode pembayarannya sendiri.
     */
    public function pay(Request $request, string $invoiceNumber): RedirectResponse
    {
        $payment = $this->findOwnedPayment($request, $invoiceNumber);

        if ($payment->isPaid()) {
            return back()->with('error', 'Pembayaran invoice ini sudah lunas.');
        }

        if (! $payment->canBePaid()) {
            return back()->with('error', 'Pembayaran tidak dapat dilanjutkan. Batas waktu pembayaran telah berakhir.');
        }

        if (! $this->midtrans->isConfigured()) {
            Log::error('Midtrans diklik bayar tanpa Server Key yang dikonfigurasi.');

            return back()->with('error', 'Pembayaran online belum dapat dibuka. Hubungi tim GoSport untuk bantuan.');
        }

        try {
            $payment = $this->payments->ensureCharge($payment);
        } catch (PaymentException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Gagal membuat transaksi Midtrans.', [
                'invoice_number' => $payment->invoice_number,
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Gagal terhubung ke payment gateway. Silakan coba beberapa saat lagi.');
        }

        if (! $payment->hasPaymentUrl()) {
            return back()->with('error', 'Halaman pembayaran tidak tersedia. Silakan hubungi tim GoSport.');
        }

        return redirect()->away($payment->payment_url);
    }

    /**
     * Invoice hanya boleh dilihat oleh pemilik pesanan.
     */
    private function findOwnedPayment(Request $request, string $invoiceNumber, bool $withRelation = false): Payment
    {
        $query = Payment::where('invoice_number', $invoiceNumber)
            ->whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id));

        if ($withRelation) {
            $query->with(['order.items', 'order.user']);
        }

        return $query->firstOrFail();
    }
}
