@extends('layouts.admin')

@section('title', 'Detail Pesanan - GoSport')
@section('admin-title', 'Detail Pesanan')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-600 hover:text-brand-600">
            <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke daftar pesanan
        </a>
        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Hapus pesanan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50">
                Hapus Pesanan
            </button>
        </form>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
        <div class="space-y-6">
            <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-neutral-400">Nomor Pesanan</p>
                        <p class="text-xl font-extrabold text-neutral-900">{{ $order->order_number }}</p>
                        <p class="mt-1 text-sm text-neutral-500">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                    </div>
                    <span class="rounded-full bg-brand-50 px-4 py-1.5 text-xs font-bold text-brand-600">
                        {{ \App\Models\Order::statusLabel($order->status) }}
                    </span>
                </div>

                <div class="mt-6 border-t border-neutral-100 pt-5">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-neutral-900">Ubah Status Pesanan</h3>
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-3 flex flex-wrap gap-3">
                        @csrf
                        @method('PUT')
                        <select name="status" class="rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm outline-none focus:border-brand-500">
                            @foreach (\App\Models\Order::STATUSES as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-600">Perbarui Status</button>
                    </form>
                </div>
            </div>

            <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-extrabold text-neutral-900">Produk Dipesan</h2>
                <div class="mt-4 divide-y divide-neutral-100">
                    @foreach ($order->items as $item)
                        <div class="flex gap-4 py-4">
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="h-16 w-16 shrink-0 rounded-xl object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-neutral-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-neutral-500">
                                    {{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}
                                    @if ($item->size) &middot; {{ $item->size }} @endif
                                    @if ($item->color) &middot; {{ $item->color }} @endif
                                </p>
                            </div>
                            <p class="text-sm font-bold text-neutral-900">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <dl class="mt-4 space-y-3 border-t border-neutral-100 pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-neutral-500">Subtotal</dt><dd class="font-semibold text-neutral-900">{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-neutral-500">Pengiriman</dt><dd class="font-semibold text-neutral-900">{{ 'Rp ' . number_format($order->shipping_fee, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-neutral-500">Diskon</dt><dd class="font-semibold text-green-600">- {{ 'Rp ' . number_format($order->discount, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between border-t border-neutral-100 pt-3 text-base"><dt class="font-bold text-neutral-900">Total</dt><dd class="font-black text-brand-600">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-extrabold text-neutral-900">Customer</h2>
                <div class="mt-4 flex items-center gap-3">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-neutral-900 text-lg font-bold text-white">
                        {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                    </span>
                    <div>
                        <p class="text-sm font-bold text-neutral-900">{{ $order->user->name ?? '-' }}</p>
                        <p class="text-xs text-neutral-500">{{ $order->user->email ?? '-' }}</p>
                    </div>
                </div>
                @if ($order->user)
                    <a href="{{ route('admin.customers.show', $order->user) }}" class="mt-4 inline-block text-sm font-semibold text-brand-600 hover:underline">Lihat profil customer</a>
                @endif
            </div>

            <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-extrabold text-neutral-900">Pengiriman</h2>
                <div class="mt-4 text-sm text-neutral-600">
                    <p class="font-bold text-neutral-900">{{ $order->recipient_name }}</p>
                    <p>{{ $order->recipient_phone }}</p>
                    <p class="mt-1">{{ $order->full_address }}, {{ $order->district }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                    <p class="mt-3 rounded-xl bg-neutral-50 px-3 py-2 text-xs">{{ $order->courier }} - {{ $order->shipping_service }}</p>
                    @if ($order->note)
                        <p class="mt-3 rounded-xl bg-neutral-50 px-3 py-2 text-xs">Catatan: {{ $order->note }}</p>
                    @endif
                </div>
            </div>

            <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-extrabold text-neutral-900">Pembayaran</h2>
                @if ($order->payment)
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-neutral-500">Invoice</dt><dd class="font-mono font-semibold text-neutral-900">#{{ $order->payment->invoice_number }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Metode</dt><dd class="font-semibold text-neutral-900">{{ $order->payment->methodLabel() }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Status</dt><dd><x-payment.status-pill :status="$order->payment->payment_status" /></dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Total Invoice</dt><dd class="font-semibold text-neutral-900">{{ $order->payment->amountLabel() }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Gateway</dt><dd class="font-semibold text-neutral-900">{{ $order->payment->gateway ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Metode Dipilih</dt><dd class="font-semibold text-neutral-900">{{ $order->payment->paymentTypeLabel() ?? '-' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="shrink-0 text-neutral-500">ID Transaksi</dt><dd class="break-all text-right font-mono text-neutral-900">{{ $order->payment->gateway_reference ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Status Midtrans</dt><dd class="font-mono uppercase text-neutral-900">{{ $order->payment->transaction_status ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Fraud Status</dt><dd class="font-mono uppercase text-neutral-900">{{ $order->payment->fraud_status ?? '-' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="shrink-0 text-neutral-500">Nomor Virtual Account</dt><dd class="select-all break-all text-right font-mono font-bold text-neutral-900">{{ $order->payment->account_number ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">BankVA</dt><dd class="font-semibold uppercase text-neutral-900">{{ $order->payment->bank_name ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Batas Bayar</dt><dd class="font-semibold text-neutral-900">{{ $order->payment->expired_at?->translatedFormat('d F Y, H:i') ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Waktu Bayar</dt><dd class="font-semibold {{ $order->payment->paid_at ? 'text-green-600' : 'text-neutral-400' }}">{{ $order->payment->paid_at?->translatedFormat('d F Y, H:i') ?? '-' }}</dd></div>
                    </dl>

                    @if ($order->payment->payment_method === 'midtrans')
                        @if (! $order->payment->isPaid())
                            <p class="mt-4 rounded-xl bg-blue-50 p-3.5 text-xs text-blue-800">
                                <span class="font-bold">Info Midtrans:</span> Status pembayaran Midtrans diperbarui secara otomatis melalui callback webhook sistem Midtrans dan tidak dapat diverifikasi manual dari dashboard admin.
                            </p>
                        @endif
                    @elseif (! $order->payment->isPaid())
                        <form action="{{ route('admin.orders.payment.verify', $order) }}" method="POST" class="mt-4"
                            onsubmit="return confirm('Pastikan pembayaran {{ $order->payment->invoice_number }} sudah benar-benar diterima sebelum verifikasi manual?')">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-600">
                                Verifikasi Pembayaran Manual
                            </button>
                        </form>
                    @endif
                @else
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-neutral-500">Metode</dt><dd class="font-semibold text-neutral-900">{{ config('gosport.payment_methods')[$order->payment_method] ?? $order->payment_method }}</dd></div>
                        <div class="flex justify-between"><dt class="text-neutral-500">Status</dt><dd class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">{{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}</dd></div>
                    </dl>
                    <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs text-amber-800">
                        Pesanan ini dibuat sebelum fitur invoice tersedia sehingga tidak memiliki tagihan.
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
