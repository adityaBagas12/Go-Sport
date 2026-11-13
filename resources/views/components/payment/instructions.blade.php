@props(['payment'])

@php
    $isProcessing = $payment->payment_status === 'processing';
    $isExpired = $payment->isExpired();
    $isFailed = in_array($payment->payment_status, ['denied', 'cancelled', 'expired', 'failed'], true);
    $isCod = $payment->payment_method === 'cod';
@endphp

<div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
    <h2 class="flex items-center gap-2 text-lg font-extrabold text-[#0A2A5E]">
        <x-icon :name="$isCod ? 'truck' : 'credit-card'" class="h-5 w-5 text-[#0A6CFF]" />
        {{ $payment->methodLabel() }}
    </h2>

    {{-- Detail metode yang benar-benar dipilih customer, berasal dari Midtrans --}}
    @if ($payment->payment_type)
        <div class="mt-5 grid gap-px overflow-hidden rounded-2xl border border-[#8CC4FF] bg-[#8CC4FF] sm:grid-cols-2">
            <div class="bg-[#E6F1FF] p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-[#0A6CFF]">Metode Pembayaran</p>
                <p class="mt-1 text-lg font-extrabold text-[#0A2A5E]">{{ $payment->paymentTypeLabel() }}</p>
            </div>
            @if ($payment->payment_type === 'bank_transfer' && $payment->bank_name)
                <div class="bg-[#E6F1FF] p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-[#0A6CFF]">Bank</p>
                    <p class="mt-1 text-lg font-extrabold uppercase text-[#0A2A5E]">{{ $payment->bank_name }}</p>
                </div>
            @endif
            @if ($payment->hasAccountNumber())
                <div class="bg-[#E6F1FF] p-4 sm:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-wide text-[#0A6CFF]">
                        Nomor Virtual Account
                    </p>
                    <p class="mt-1 select-all break-all font-mono text-xl font-extrabold tracking-wider text-[#0A2A5E]">
                        {{ $payment->account_number }}
                    </p>
                    <p class="mt-1 text-xs text-neutral-500">Nomor ini diterbitkan oleh Midtrans.</p>
                </div>
            @endif
            <div class="bg-[#E6F1FF] p-4 sm:col-span-2">
                <p class="text-xs font-bold uppercase tracking-wide text-[#0A6CFF]">Total yang harus dibayar</p>
                <p class="mt-0.5 text-2xl font-black text-[#0A2A5E]">{{ $payment->amountLabel() }}</p>
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
            <p class="text-sm font-bold text-neutral-900">Instruksi Pembayaran</p>
            <ol class="mt-3 space-y-2">
                @foreach ($payment->instructions as $step)
                    <li class="flex items-start gap-2.5 text-sm text-neutral-600">
                        <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-brand-50 text-[11px] font-bold text-brand-600">
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
        <div class="mt-6 flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700">
            <x-icon name="refresh" class="h-5 w-5 animate-spin" /> Menunggu konfirmasi pembayaran dari Midtrans
        </div>
    @endif

    {{-- Tombol bayar: hanya untuk Midtrans, tombol aksen #FF7A00 dengan teks #10213F --}}
    @if ($payment->canBePaid())
        <form action="{{ route('payment.pay', $payment->invoice_number) }}" method="POST" class="mt-6">
            @csrf
            <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#FF7A00] px-6 py-3.5 text-sm font-black text-[#10213F] shadow-lg shadow-[#FF7A00]/30 transition hover:bg-[#ff8f26]">
                <x-icon name="credit-card" class="h-5 w-5 text-[#10213F]" />
                {{ $payment->hasPaymentUrl() ? 'Lanjut ke Pembayaran' : 'Bayar Sekarang' }}
            </button>
            <p class="mt-3 text-center text-xs text-neutral-400">
                Anda akan diarahkan ke halaman pembayaran Midtrans.
            </p>
        </form>
    @endif
</div>
