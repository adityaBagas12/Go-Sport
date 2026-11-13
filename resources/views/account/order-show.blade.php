@extends('layouts.account')

@section('account-title', 'Detail Pesanan')
@section('title', 'Detail Pesanan - GoSport')

@section('account-content')
    @php
        $steps = ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai'];
        $currentIndex = array_search($order->status, $steps, true);
        $isCancelled = $order->status === 'dibatalkan';
    @endphp

    <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-neutral-400">Nomor Pesanan</p>
                <p class="text-xl font-extrabold text-neutral-900">{{ $order->order_number }}</p>
                <p class="mt-1 text-sm text-neutral-500">Dibuat {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
            <div class="text-right">
                <span class="inline-flex rounded-full px-4 py-1.5 text-xs font-bold {{ $isCancelled ? 'bg-red-100 text-red-700' : 'bg-brand-50 text-brand-600' }}">
                    {{ \App\Models\Order::statusLabel($order->status) }}
                </span>
                <p class="mt-2 text-xs text-neutral-500">Pembayaran: <span class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">{{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}</span></p>
            </div>
        </div>

        @unless ($isCancelled)
            <div class="mt-8">
                <div class="flex items-center">
                    @foreach ($steps as $index => $step)
                        <div class="flex flex-1 items-center {{ $loop->last ? 'flex-none' : '' }}">
                            <div class="flex flex-col items-center">
                                <span class="grid h-10 w-10 place-items-center rounded-full text-sm font-bold {{ $index <= $currentIndex ? 'bg-brand-500 text-white' : 'bg-neutral-100 text-neutral-400' }}">
                                    @if ($index < $currentIndex)
                                        <x-icon name="check" class="h-5 w-5" />
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </span>
                                <span class="mt-2 w-24 text-center text-xs font-semibold {{ $index <= $currentIndex ? 'text-neutral-900' : 'text-neutral-400' }}">
                                    {{ \App\Models\Order::statusLabel($step) }}
                                </span>
                            </div>
                            @unless ($loop->last)
                                <div class="mx-2 mb-6 h-1 flex-1 rounded-full {{ $index < $currentIndex ? 'bg-brand-500' : 'bg-neutral-100' }}"></div>
                            @endunless
                        </div>
                    @endforeach
                </div>
            </div>
        @endunless

        @if ($order->status === 'menunggu_pembayaran')
            <div class="mt-6 flex flex-wrap gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                @if ($order->payment)
                    <p class="flex-1 text-sm text-amber-800">
                        Selesaikan pembayaran melalui invoice
                        <span class="font-bold">#{{ $order->payment->invoice_number }}</span>
                        ({{ $order->payment->methodLabel() }}) agar pesanan dapat diproses.
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('payment.invoice', $order->payment->invoice_number) }}"
                            class="inline-flex items-center rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-600">
                            Lihat Invoice &amp; Bayar
                        </a>
                        <form action="{{ route('order.cancel', $order->order_number) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50">Batalkan</button>
                        </form>
                    </div>
                @else
                    <p class="flex-1 text-sm text-amber-800">
                        Invoice pembayaran untuk pesanan ini belum tersedia.
                        Hubungi tim GoSport untuk assistance.
                    </p>
                    <form action="{{ route('order.cancel', $order->order_number) }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50">Batalkan</button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 text-lg font-extrabold text-neutral-900">
                <x-icon name="map-pin" class="h-5 w-5 text-brand-500" /> Alamat Pengiriman
            </h2>
            <div class="mt-4 text-sm text-neutral-600">
                <p class="font-bold text-neutral-900">{{ $order->recipient_name }}</p>
                <p>{{ $order->recipient_phone }}</p>
                <p class="mt-1">{{ $order->full_address }}, {{ $order->district }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                @if ($order->note)
                    <p class="mt-3 rounded-xl bg-neutral-50 px-3 py-2 text-xs text-neutral-500">Catatan: {{ $order->note }}</p>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 text-lg font-extrabold text-neutral-900">
                <x-icon name="truck" class="h-5 w-5 text-brand-500" /> Pengiriman & Pembayaran
            </h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-neutral-500">Jasa Kirim</dt>
                    <dd class="font-semibold text-neutral-900">{{ $order->courier }} - {{ $order->shipping_service }}</dd>
                </div>
                @if ($order->payment)
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Nomor Invoice</dt>
                        <dd class="font-mono font-semibold text-neutral-900">#{{ $order->payment->invoice_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Metode Bayar</dt>
                        <dd class="font-semibold text-neutral-900">{{ $order->payment->methodLabel() }}</dd>
                    </div>
                    @if ($order->payment->payment_type)
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Metode Terpilih</dt>
                            <dd class="font-semibold text-neutral-900">{{ $order->payment->paymentTypeLabel() }}</dd>
                        </div>
                    @endif
                    @if ($order->payment->hasAccountNumber())
                        <div class="flex justify-between gap-4">
                            <dt class="shrink-0 text-neutral-500">Nomor Virtual Account</dt>
                            <dd class="select-all break-all text-right font-mono font-bold text-neutral-900">
                                {{ $order->payment->bank_name ? strtoupper($order->payment->bank_name).' ' : '' }}{{ $order->payment->account_number }}
                            </dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Status Invoice</dt>
                        <dd class="font-semibold text-neutral-900">{{ \App\Models\Payment::statusLabel($order->payment->payment_status) }}</dd>
                    </div>
                @else
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Metode Bayar</dt>
                        <dd class="font-semibold text-neutral-900">{{ config('gosport.payment_methods')[$order->payment_method] ?? $order->payment_method }}</dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-neutral-500">Status Bayar</dt>
                    <dd class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">{{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-neutral-900">Rincian Produk</h2>

        <div class="mt-4 divide-y divide-neutral-100">
            @foreach ($order->items as $item)
                <div class="flex gap-4 py-4">
                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="h-20 w-20 shrink-0 rounded-xl object-cover">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-neutral-900">{{ $item->product_name }}</p>
                        <p class="mt-0.5 text-xs text-neutral-500">
                            {{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}
                            @if ($item->size) &middot; Ukuran {{ $item->size }} @endif
                            @if ($item->color) &middot; {{ $item->color }} @endif
                        </p>
                    </div>
                    <p class="text-sm font-extrabold text-neutral-900">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        <dl class="mt-4 space-y-3 border-t border-neutral-100 pt-4 text-sm">
            <div class="flex justify-between">
                <dt class="text-neutral-500">Subtotal</dt>
                <dd class="font-semibold text-neutral-900">{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-neutral-500">Biaya Pengiriman</dt>
                <dd class="font-semibold text-neutral-900">{{ 'Rp ' . number_format($order->shipping_fee, 0, ',', '.') }}</dd>
            </div>
            @if ($order->discount > 0)
                <div class="flex justify-between text-green-600">
                    <dt>Diskon</dt>
                    <dd class="font-semibold">- {{ 'Rp ' . number_format($order->discount, 0, ',', '.') }}</dd>
                </div>
            @endif
            <div class="flex justify-between border-t border-neutral-100 pt-3 text-base">
                <dt class="font-bold text-neutral-900">Total Pembayaran</dt>
                <dd class="font-black text-brand-600">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</dd>
            </div>
        </dl>
    </div>
@endsection
