@extends('layouts.app')

@section('title', 'Invoice #'.$payment->invoice_number.' - GoSport')

{{-- Status masih diproses: muat ulang berkala supaya halaman ikut terupdate
     begitu notification Midtrans diterima. --}}
@if ($payment->payment_status === 'processing' && ! $payment->isExpired())
    @push('head')
        <meta http-equiv="refresh" content="15">
    @endpush
@endif

@section('content')
    @php
        $order = $payment->order;
        $isPaid = $payment->isPaid();
        $isProcessing = $payment->payment_status === 'processing';
        $isExpired = $payment->isExpired();
        $isFailed = in_array($payment->payment_status, ['denied', 'cancelled', 'expired', 'failed'], true);
    @endphp

    <div class="border-b border-[#8CC4FF]/30 bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-[#0A6CFF]">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <a href="{{ route('account.orders') }}" class="hover:text-[#0A6CFF]">Pesanan</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="font-medium text-[#10213F]">Invoice</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-[#0A2A5E]">Invoice GoSport</h1>
        </div>
    </div>

    <div class="container-page py-10">
        <div class="mx-auto max-w-[560px] space-y-5">
            <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-neutral-400">Nomor Invoice</p>
                        <p class="text-xl font-extrabold text-[#10213F]">#{{ $payment->invoice_number }}</p>
                        <p class="mt-1 text-sm text-neutral-500">
                            Tanggal {{ $payment->created_at->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                        <p class="mt-0.5 text-sm text-neutral-500">
                            Pesanan <span class="font-semibold text-[#10213F]">{{ $order->order_number }}</span>
                        </p>
                    </div>
                    <x-payment.status-pill :status="$payment->payment_status" />
                </div>

                @if ($isPaid)
                    <div class="mt-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                        <x-icon name="check-circle" class="h-5 w-5 shrink-0 text-emerald-600" />
                        <p class="text-sm text-emerald-800">
                            <span class="font-bold">Pembayaran berhasil</span> pada
                            {{ $payment->paid_at?->translatedFormat('d F Y, H:i') }} WIB. Pesanan segera diproses.
                        </p>
                    </div>
                @elseif ($isFailed)
                    <div class="mt-5 flex items-center gap-3 rounded-2xl border border-[#E53935]/40 bg-[#E53935]/10 p-4">
                        <x-icon name="x" class="h-5 w-5 shrink-0 text-[#E53935]" />
                        <p class="text-sm text-[#E53935]">
                            <span class="font-bold">{{ \App\Models\Payment::statusLabel($payment->payment_status) }}</span>.
                            Silakan buat pesanan baru atau hubungi tim GoSport untuk bantuan.
                        </p>
                    </div>
                @elseif ($isExpired)
                    <div class="mt-5 flex items-center gap-3 rounded-2xl border border-[#E53935]/40 bg-[#E53935]/10 p-4">
                        <x-icon name="x" class="h-5 w-5 shrink-0 text-[#E53935]" />
                        <p class="text-sm text-[#E53935]">Batas pembayaran telah berakhir. Silakan hubungi kami untuk bantuan.</p>
                    </div>
                @elseif ($isProcessing)
                    <div class="mt-5 flex items-center gap-3 rounded-2xl border border-[#8CC4FF] bg-[#E6F1FF] p-4">
                        <x-icon name="clock" class="h-5 w-5 shrink-0 text-[#0A6CFF]" />
                        <p class="text-sm text-[#0A2A5E]">
                            Pembayaran Anda sedang <span class="font-bold">diverifikasi</span> oleh Midtrans.
                            Status akan diperbarui otomatis setelah Midtrans mengirim konfirmasi.
                        </p>
                    </div>
                @endif

                <div class="mt-6 grid gap-3 rounded-2xl border border-[#8CC4FF]/40 bg-[#E6F1FF] p-4 sm:grid-cols-2">
                    <div class="text-center sm:col-span-2">
                        <p class="text-xs uppercase tracking-wide text-neutral-500">Total Pembayaran</p>
                        <p class="mt-1 text-3xl font-black text-[#10213F]">{{ $payment->amountLabel() }}</p>
                        <p class="mt-2 inline-flex items-center gap-2 rounded-full bg-[#0A2A5E] px-4 py-1.5 text-xs font-bold text-white shadow-sm">
                            <x-icon name="credit-card" class="h-4 w-4 text-[#FF7A00]" /> {{ $payment->methodLabel() }}
                        </p>
                    </div>
                    <div class="border-t border-[#8CC4FF]/30 pt-3 sm:col-span-2 sm:flex sm:items-center sm:justify-between sm:border-0 sm:pt-0 sm:text-left">
                        <span class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Nomor Invoice</span>
                        <span class="mt-0.5 block font-mono text-sm font-bold text-[#10213F] sm:mt-0">{{ $payment->invoice_number }}</span>
                    </div>
                    <div class="border-t border-[#8CC4FF]/30 pt-3 sm:flex sm:items-center sm:justify-between sm:border-0 sm:pt-0">
                        <span class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Tanggal</span>
                        <span class="mt-0.5 block text-sm font-semibold text-[#10213F] sm:mt-0">{{ $payment->created_at->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="border-t border-[#8CC4FF]/30 pt-3 sm:flex sm:items-center sm:justify-between sm:border-0 sm:pt-0">
                        <span class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Status Bayar</span>
                        <span class="mt-0.5 block text-sm font-semibold text-[#10213F] sm:mt-0">{{ \App\Models\Payment::statusLabel($payment->payment_status) }}</span>
                    </div>
                    @if ($payment->payment_type)
                        <div class="border-t border-[#8CC4FF]/30 pt-3 sm:flex sm:items-center sm:justify-between sm:border-0 sm:pt-0">
                            <span class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Metode Terpilih</span>
                            <span class="mt-0.5 block text-sm font-semibold text-[#10213F] sm:mt-0">{{ $payment->paymentTypeLabel() }}</span>
                        </div>
                    @endif
                    @if ($payment->gateway_reference)
                        <div class="border-t border-[#8CC4FF]/30 pt-3 sm:flex sm:items-center sm:justify-between sm:border-0 sm:pt-0">
                            <span class="text-xs font-semibold uppercase tracking-wide text-neutral-500">ID Transaksi Midtrans</span>
                            <span class="mt-0.5 block font-mono text-xs text-neutral-600 sm:mt-0">{{ $payment->gateway_reference }}</span>
                        </div>
                    @endif
                    @if ($payment->transaction_status)
                        <div class="border-t border-[#8CC4FF]/30 pt-3 sm:flex sm:items-center sm:justify-between sm:border-0 sm:pt-0">
                            <span class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Status Midtrans</span>
                            <span class="mt-0.5 block font-mono text-xs uppercase text-neutral-600 sm:mt-0">{{ $payment->transaction_status }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <x-payment.instructions :payment="$payment" />

            <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
                <h2 class="text-lg font-extrabold text-[#0A2A5E]">Rincian Pesanan</h2>

                <div class="mt-4 divide-y divide-[#8CC4FF]/20">
                    @foreach ($order->items as $item)
                        <div class="flex gap-3 py-3">
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="h-12 w-12 shrink-0 rounded-xl object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="line-clamp-2 text-sm font-semibold text-[#10213F]">{{ $item->product_name }}</p>
                                <p class="mt-0.5 text-xs text-neutral-500">
                                    {{ $item->quantity }}x @ {{ number_format($item->price, 0, ',', '.') }}
                                    @if ($item->size) &middot; {{ $item->size }} @endif
                                    @if ($item->color) &middot; {{ $item->color }} @endif
                                </p>
                            </div>
                            <p class="text-sm font-bold text-[#10213F]">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <dl class="mt-2 space-y-3 border-t border-[#8CC4FF]/30 pt-4 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Subtotal ({{ $order->items->sum('quantity') }} item)</dt>
                        <dd class="font-semibold text-[#10213F]">{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</dd>
                    </div>
                    @if ($order->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <dt class="text-neutral-500">Diskon</dt>
                            <dd class="font-semibold">- {{ 'Rp ' . number_format($order->discount, 0, ',', '.') }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Ongkos Kirim ({{ $order->courier }} {{ $order->shipping_service }})</dt>
                        <dd class="font-semibold text-[#10213F]">{{ 'Rp ' . number_format($order->shipping_fee, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-[#8CC4FF]/30 pt-3 text-base">
                        <dt class="font-bold text-[#10213F]">Total Pembayaran</dt>
                        <dd class="font-black text-[#0A6CFF]">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                <div class="mt-5 grid gap-3 rounded-2xl border border-[#8CC4FF]/30 bg-[#F4F8FF] p-4 text-sm sm:grid-cols-2">
                    <div>
                        <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-neutral-400">
                            <x-icon name="user" class="h-3.5 w-3.5" /> Customer
                        </p>
                        <p class="mt-1 font-bold text-[#10213F]">{{ $order->user?->name ?? '-' }}</p>
                        <p class="text-xs text-neutral-500">{{ $order->user?->email ?? '-' }}</p>
                        <p class="text-xs text-neutral-500">{{ $order->recipient_name }} &middot; {{ $order->recipient_phone }}</p>
                    </div>
                    <div>
                        <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-neutral-400">
                            <x-icon name="truck" class="h-3.5 w-3.5" /> Pengiriman
                        </p>
                        <p class="mt-1 font-bold text-[#10213F]">{{ $order->courier }} - {{ $order->shipping_service }}</p>
                        <p class="text-xs text-neutral-500">{{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                        <p class="text-xs text-neutral-500">{{ $order->recipient_name }}</p>
                    </div>
                </div>

                <a href="{{ route('order.show', $order->order_number) }}"
                    class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-[#8CC4FF] bg-white px-6 py-3 text-sm font-bold text-[#0A6CFF] transition hover:bg-[#E6F1FF]">
                    <x-icon name="clipboard" class="h-5 w-5" /> Lihat Detail Pesanan
                </a>
            </div>

            <p class="pb-4 text-center text-xs text-neutral-400">
                Ada kendala pembayaran? Hubungi tim GoSport melalui halaman kontak.
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const countdownEl = document.getElementById('countdown-label');
        if (countdownEl) {
            const deadline = parseInt(countdownEl.dataset.deadline, 10) * 1000;
            const fmt = (n) => String(n).padStart(2, '0');

            const tick = () => {
                const diff = Math.floor((deadline - Date.now()) / 1000);
                if (diff <= 0) {
                    countdownEl.textContent = countdownEl.dataset.expiredLabel;
                    clearInterval(interval);
                    return;
                }
                const days = Math.floor(diff / 86400);
                const hours = Math.floor((diff % 86400) / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                const seconds = diff % 60;
                countdownEl.textContent = (days > 0 ? days + ' hari, ' : '') + fmt(hours) + ':' + fmt(minutes) + ':' + fmt(seconds);
            };

            tick();
            const interval = setInterval(tick, 1000);
        }
    </script>
@endpush
