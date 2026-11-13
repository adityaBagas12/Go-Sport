@extends('layouts.account')

@section('account-title', 'Dashboard')
@section('title', 'Akun Saya - GoSport')

@section('account-content')
    <div class="grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['clipboard', $orders->count(), 'Total Pesanan'],
            ['heart', $wishlistCount, 'Wishlist'],
            ['map-pin', $addressCount, 'Alamat Tersimpan'],
        ] as [$icon, $value, $label])
            <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-500">
                    <x-icon :name="$icon" class="h-6 w-6" />
                </span>
                <p class="mt-4 text-3xl font-black text-neutral-900">{{ $value }}</p>
                <p class="text-sm text-neutral-500">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-neutral-900">Pesanan Terbaru</h2>
            <a href="{{ route('account.orders') }}" class="text-sm font-semibold text-brand-600 hover:underline">Lihat semua</a>
        </div>

        @forelse ($orders as $order)
            <a href="{{ route('order.show', $order->order_number) }}"
                class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-neutral-100 p-4 transition hover:border-brand-300 hover:bg-brand-50/30">
                <div>
                    <p class="text-sm font-bold text-neutral-900">{{ $order->order_number }}</p>
                    <p class="text-xs text-neutral-500">{{ $order->created_at->translatedFormat('d F Y, H:i') }} &middot; {{ $order->items()->count() }} produk</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-extrabold text-neutral-900">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</p>
                    <span class="text-xs font-semibold text-brand-600">{{ \App\Models\Order::statusLabel($order->status) }}</span>
                </div>
            </a>
        @empty
            <div class="mt-4 rounded-2xl bg-neutral-50 py-12 text-center">
                <p class="text-sm text-neutral-500">Belum ada pesanan. Yuk mulai belanja!</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-xl bg-neutral-900 px-6 py-3 text-sm font-bold text-white hover:bg-brand-500">
                    Belanja Sekarang
                </a>
            </div>
        @endforelse
    </div>
@endsection
