@props(['payment'])

@php
    $isProcessing = $payment->payment_status === 'processing';
    $isExpired = $payment->isExpired();
    $isFailed = in_array($payment->payment_status, ['denied', 'cancelled', 'expired', 'failed'], true);
    $isCod = $payment->payment_method === 'cod';
@endphp

<div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
    <h2 class="flex items-center gap-2 text-lg font-extrabold text-[#1E2B24]">
        <x-icon :name="$isCod ? 'truck' : 'credit-card'" class="h-5 w-5 text-[#4F7A63]" />
        {{ $payment->methodLabel() }}
    </h2>

    {{-- Detail metode yang benar-benar dipilih customer, berasal dari Midtrans --}}
    @if ($payment->payment_type)
        <div class="mt-5 grid gap-px overflow-hidden rounded-2xl border border-[#D5E0D8] bg-[#D5E0D8] sm:grid-cols-2">
            <div class="bg-[#F6F9F7] p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-[#4F7A63]">Metode Pembayaran</p>
                <p class="mt-1 text-lg font-extrabold text-[#1E2B24]">{{ $payment->paymentTypeLabel() }}</p>
            </div>
            @if ($payment->payment_type === 'bank_transfer' && $payment->bank_name)
                <div class="bg-[#F6F9F7] p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-[#4F7A63]">Bank</p>
                    <p class="mt-1 text-lg font-extrabold uppercase text-[#1E2B24]">{{ $payment->bank_name }}</p>
                </div>
            @endif
            @if ($payment->hasAccountNumber())
                <div class="bg-[#F6F9F7] p-4 sm:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-wide text-[#4F7A63]">
                        Nomor Virtual Account
                    </p>
                    <p class="mt-1 select-all break-all font-mono text-xl font-extrabold tracking-wider text-[#1E2B24]">
                        {{ $payment->account_number }}
                    </p>
                    <p class="mt-1 text-xs text-[#5A6B61]">Nomor ini diterbitkan oleh Midtrans.</p>
                </div>
            @endif
            <div class="bg-[#F6F9F7] p-4 sm:col-span-2">
                <p class="text-xs font-bold uppercase tracking-wide text-[#4F7A63]">Total yang harus dibayar</p>
                <p class="mt-0.5 text-2xl font-black text-[#4F7A63]">{{ $payment->amountLabel() }}</p>
            </div>
        </div>
    @endif

    {{-- Batas pembayaran --}}
    @if ($payment->expired_at && ! $isCod && ! $payment->isPaid())
        <div class="mt-4 flex items-center justify-between rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
            <span class="flex items-center gap-2 text-sm font-semibold text-amber-800">
                <x-icon name="clock" class="h-4 w-4" /> Batas pembayaran
            </span>
            <span class="text-sm font-bold text-amber-900" id="countdown-label"
                data-deadline="{{ $payment->expired_at->timestamp }}"
                data-expired-label="Waktu habis">
                {{ $payment->expired_at->translatedFormat('d F Y, H:i') }}
            </span>
        </div>
    @endif

    {{-- Langkah pembayaran --}}
    @if ($payment->instructions && ! $payment->isPaid())
        <div class="mt-5">
            <p class="text-sm font-bold text-[#1E2B24]">Instruksi Pembayaran</p>
            <ol class="mt-3 space-y-2">
                @foreach ($payment->instructions as $step)
                    <li class="flex items-start gap-2.5 text-sm text-[#1E2B24]">
                        <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-[#4F7A63]/10 text-[11px] font-bold text-[#4F7A63]">
                            {{ $loop->iteration }}
                        </span>
                        <span>{{ $step }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    {{-- Aksi customer --}}
    @if ($payment->isPaid())
        <div class="mt-6 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            <x-icon name="check-circle" class="h-5 w-5" /> Pembayaran lunas
        </div>
    @elseif ($isFailed)
        <div class="mt-6 flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
            <x-icon name="x" class="mt-0.5 h-5 w-5 shrink-0" />
            <span>
                Pembayaran {{ \App\Models\Payment::statusLabel($payment->payment_status) }}.
                Anda dapat membuat pesanan baru atau menghubungi tim GoSport.
            </span>
        </div>
    @elseif ($isExpired)
        <div class="mt-6 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
            <x-icon name="x" class="h-5 w-5" /> Batas pembayaran telah berakhir
        </div>
    @elseif ($isProcessing)
        <div class="mt-6 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-[#2F4F3E]">
            <x-icon name="refresh" class="h-5 w-5 animate-spin text-[#4F7A63]" /> Menunggu konfirmasi pembayaran dari Midtrans
        </div>
    @endif

    {{-- Tombol bayar: hanya untuk Midtrans --}}
    @if ($payment->canBePaid())
        <form action="{{ route('payment.pay', $payment->invoice_number) }}" method="POST" class="mt-6">
            @csrf
            <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-[#4F7A63]/30 transition hover:bg-[#3E6350]">
                <x-icon name="credit-card" class="h-5 w-5 text-white" />
                {{ $payment->hasPaymentUrl() ? 'Lanjut ke Pembayaran' : 'Bayar Sekarang' }}
            </button>
            <p class="mt-3 text-center text-xs text-[#5A6B61]">
                Anda akan diarahkan ke halaman pembayaran Midtrans.
            </p>
        </form>
    @endif
</div>
