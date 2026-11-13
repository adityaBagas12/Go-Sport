@extends('layouts.app')

@section('title', $title.' - GoSport')

@section('content')
    @php
        $tones = [
            'success' => ['border-emerald-200 bg-emerald-50 text-emerald-800', 'check-circle', 'text-emerald-600'],
            'warning' => ['border-amber-200 bg-amber-50 text-amber-800', 'clock', 'text-amber-600'],
            'danger' => ['border-red-200 bg-red-50 text-red-800', 'x', 'text-red-600'],
        ];
        [$boxClass, $icon, $iconClass] = $tones[$tone] ?? $tones['warning'];
    @endphp

    <div class="container-page py-16">
        <div class="mx-auto max-w-lg space-y-5">
            <div class="rounded-3xl border border-neutral-100 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-full {{ $iconClass }} {{ $boxClass }}">
                    <x-icon :name="$icon" class="h-7 w-7" />
                </div>

                <h1 class="mt-5 text-2xl font-extrabold text-neutral-900">{{ $title }}</h1>

                <p class="mt-3 text-sm leading-relaxed text-neutral-600">{{ $message }}</p>

                <p class="mt-4 rounded-xl bg-neutral-50 p-3 text-xs text-neutral-500">
                    Status pembayaran pada invoice hanya berubah setelah Midtrans mengirim
                    konfirmasi ke sistem kami. Halaman ini tidak mengubah status pembayaran.
                </p>

                <div class="mt-6 space-y-3">
                    @if ($invoice?->payment)
                        <a href="{{ route('payment.invoice', $invoice->payment->invoice_number) }}"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600">
                            <x-icon name="clipboard" class="h-5 w-5" /> Lihat Invoice
                        </a>
                    @endif

                    <a href="{{ route('account.orders') }}"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-neutral-200 px-6 py-3 text-sm font-bold text-neutral-700 transition hover:border-brand-400 hover:text-brand-600">
                        <x-icon name="clipboard" class="h-5 w-5" /> Riwayat Pesanan
                    </a>
                </div>
            </div>

            <p class="text-center text-xs text-neutral-400">
                Ada kendala pembayaran? Hubungi tim GoSport melalui halaman kontak.
            </p>
        </div>
    </div>
@endsection
