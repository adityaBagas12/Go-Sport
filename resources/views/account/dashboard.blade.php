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
            <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-[#4F7A63]/10 text-[#4F7A63]">
                    <x-icon :name="$icon" class="h-6 w-6" />
                </span>
                <p class="mt-4 text-3xl font-black text-[#1E2B24]">{{ $value }}</p>
                <p class="text-sm text-[#5A6B61]">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Pesanan Terbaru</h2>
            <a href="{{ route('account.orders') }}" class="text-sm font-bold text-[#4F7A63] hover:underline">Lihat semua</a>
        </div>

        @forelse ($orders as $order)
            <a href="{{ route('order.show', $order->order_number) }}"
                class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-[#D5E0D8] p-4 transition hover:border-[#4F7A63] hover:bg-[#F6F9F7]">
                <div>
                    <p class="text-sm font-bold text-[#1E2B24]">{{ $order->order_number }}</p>
                    <p class="text-xs text-[#5A6B61]">{{ $order->created_at->translatedFormat('d F Y, H:i') }} &middot; {{ $order->items()->count() }} produk</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-extrabold text-[#1E2B24]">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</p>
                    <span class="text-xs font-bold text-[#4F7A63]">{{ \App\Models\Order::statusLabel($order->status) }}</span>
                </div>
            </a>
        @empty
            <div class="mt-4 rounded-2xl border border-[#D5E0D8] bg-[#F6F9F7] py-12 text-center">
                <p class="text-sm text-[#1E2B24]">Belum ada pesanan. Yuk mulai belanja!</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-flex rounded-xl bg-[#4F7A63] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                    Belanja Sekarang
                </a>
            </div>
        @endforelse
    </div>
@endsection
