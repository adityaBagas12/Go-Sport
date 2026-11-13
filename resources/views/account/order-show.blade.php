@extends('layouts.account')

@section('account-title', 'Detail Pesanan')
@section('title', 'Detail Pesanan - GoSport')

@section('account-content')
    @php
        $steps = ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai'];
        $currentIndex = array_search($order->status, $steps, true);
        $isCancelled = $order->status === 'dibatalkan';
    @endphp

    <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-neutral-400">Nomor Pesanan</p>
                <p class="text-xl font-extrabold text-[#10213F]">{{ $order->order_number }}</p>
                <p class="mt-1 text-sm text-neutral-500">Dibuat {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
            <div class="text-right">
                <span class="inline-flex rounded-full px-4 py-1.5 text-xs font-bold {{ $isCancelled ? 'bg-red-100 text-[#E53935]' : 'border border-[#8CC4FF]/60 bg-[#E6F1FF] text-[#0A6CFF]' }}">
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
                                <span class="grid h-10 w-10 place-items-center rounded-full text-sm font-bold {{ $index <= $currentIndex ? 'bg-[#0A6CFF] text-white shadow-sm' : 'bg-neutral-100 text-neutral-400' }}">
                                    @if ($index < $currentIndex)
                                        <x-icon name="check" class="h-5 w-5" />
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </span>
                                <span class="mt-2 w-24 text-center text-xs font-semibold {{ $index <= $currentIndex ? 'text-[#10213F]' : 'text-neutral-400' }}">
                                    {{ \App\Models\Order::statusLabel($step) }}
                                </span>
                            </div>
                            @unless ($loop->last)
                                <div class="mx-2 mb-6 h-1 flex-1 rounded-full {{ $index < $currentIndex ? 'bg-[#0A6CFF]' : 'bg-neutral-200' }}"></div>
                            @endunless
                        </div>
                    @endforeach
                </div>
            </div>
        @endunless

        @if ($order->status === 'menunggu_pembayaran')
            <div class="mt-6 flex flex-wrap gap-3 rounded-2xl border border-[#8CC4FF] bg-[#E6F1FF] p-4">
                @if ($order->payment)
                    <p class="flex-1 text-sm text-[#0A2A5E]">
                        Selesaikan pembayaran melalui invoice
                        <span class="font-bold">#{{ $order->payment->invoice_number }}</span>
                        ({{ $order->payment->methodLabel() }}) agar pesanan dapat diproses.
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('payment.invoice', $order->payment->invoice_number) }}"
                            class="inline-flex items-center rounded-xl bg-[#FF7A00] px-4 py-2.5 text-sm font-bold text-[#10213F] shadow-sm transition hover:brightness-105">
                            Lihat Invoice &amp; Bayar
                        </a>
                        <form action="{{ route('order.cancel', $order->order_number) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-xl border border-[#E53935]/40 px-4 py-2.5 text-sm font-bold text-[#E53935] hover:bg-[#E53935]/10">Batalkan</button>
                        </form>
                    </div>
                @else
                    <p class="flex-1 text-sm text-amber-800">
                        Invoice pembayaran untuk pesanan ini belum tersedia.
                        Hubungi tim GoSport untuk assistance.
                    </p>
                    <form action="{{ route('order.cancel', $order->order_number) }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-xl border border-[#E53935]/40 px-4 py-2.5 text-sm font-bold text-[#E53935] hover:bg-[#E53935]/10">Batalkan</button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 text-lg font-extrabold text-[#0A2A5E]">
                <x-icon name="map-pin" class="h-5 w-5 text-[#0A6CFF]" /> Alamat Pengiriman
            </h2>
            <div class="mt-4 text-sm text-[#10213F]">
                <p class="font-bold text-[#10213F]">{{ $order->recipient_name }}</p>
                <p>{{ $order->recipient_phone }}</p>
                <p class="mt-1 text-neutral-600">{{ $order->full_address }}, {{ $order->district }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                @if ($order->note)
                    <p class="mt-3 rounded-xl border border-[#8CC4FF]/30 bg-[#F4F8FF] px-3 py-2 text-xs text-neutral-600">Catatan: {{ $order->note }}</p>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 text-lg font-extrabold text-[#0A2A5E]">
                <x-icon name="truck" class="h-5 w-5 text-[#0A6CFF]" /> Pengiriman & Pembayaran
            </h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-neutral-500">Jasa Kirim</dt>
                    <dd class="font-semibold text-[#10213F]">{{ $order->courier }} - {{ $order->shipping_service }}</dd>
                </div>
                @if ($order->payment)
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Nomor Invoice</dt>
                        <dd class="font-mono font-semibold text-[#10213F]">#{{ $order->payment->invoice_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Metode Bayar</dt>
                        <dd class="font-semibold text-[#10213F]">{{ $order->payment->methodLabel() }}</dd>
                    </div>
                    @if ($order->payment->payment_type)
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Metode Terpilih</dt>
                            <dd class="font-semibold text-[#10213F]">{{ $order->payment->paymentTypeLabel() }}</dd>
                        </div>
                    @endif
                    @if ($order->payment->hasAccountNumber())
                        <div class="flex justify-between gap-4">
                            <dt class="shrink-0 text-neutral-500">Nomor Virtual Account</dt>
                            <dd class="select-all break-all text-right font-mono font-bold text-[#0A6CFF]">
                                {{ $order->payment->bank_name ? strtoupper($order->payment->bank_name).' ' : '' }}{{ $order->payment->account_number }}
                            </dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Status Invoice</dt>
                        <dd class="font-semibold text-[#10213F]">{{ \App\Models\Payment::statusLabel($order->payment->payment_status) }}</dd>
                    </div>
                @else
                    <div class="flex justify-between">
                        <dt class="text-neutral-500">Metode Bayar</dt>
                        <dd class="font-semibold text-[#10213F]">{{ config('gosport.payment_methods')[$order->payment_method] ?? $order->payment_method }}</dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-neutral-500">Status Bayar</dt>
                    <dd class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">{{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-[#0A2A5E]">Rincian Produk</h2>

        <div class="mt-4 divide-y divide-[#8CC4FF]/20">
            @foreach ($order->items as $item)
                <div class="flex gap-4 py-4">
                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="h-20 w-20 shrink-0 rounded-xl object-cover">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-[#10213F]">{{ $item->product_name }}</p>
                        <p class="mt-0.5 text-xs text-neutral-500">
                            {{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}
                            @if ($item->size) &middot; Ukuran {{ $item->size }} @endif
                            @if ($item->color) &middot; {{ $item->color }} @endif
                        </p>
                    </div>
                    <p class="text-sm font-extrabold text-[#10213F]">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        <dl class="mt-4 space-y-3 border-t border-[#8CC4FF]/30 pt-4 text-sm">
            <div class="flex justify-between">
                <dt class="text-neutral-500">Subtotal</dt>
                <dd class="font-semibold text-[#10213F]">{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-neutral-500">Biaya Pengiriman</dt>
                <dd class="font-semibold text-[#10213F]">{{ 'Rp ' . number_format($order->shipping_fee, 0, ',', '.') }}</dd>
            </div>
            @if ($order->discount > 0)
                <div class="flex justify-between text-green-600">
                    <dt>Diskon</dt>
                    <dd class="font-semibold">- {{ 'Rp ' . number_format($order->discount, 0, ',', '.') }}</dd>
                </div>
            @endif
            <div class="flex justify-between border-t border-[#8CC4FF]/30 pt-3 text-base">
                <dt class="font-bold text-[#10213F]">Total Pembayaran</dt>
                <dd class="font-black text-[#0A6CFF]">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</dd>
            </div>
        </dl>
    </div>
@endsection
