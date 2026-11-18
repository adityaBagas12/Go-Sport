@extends('layouts.account')

@section('account-title', 'Riwayat Pesanan')
@section('title', 'Riwayat Pesanan - GoSport')

@section('account-content')
    <div class="space-y-4">
        @forelse ($orders as $order)
            <div class="rounded-3xl border border-[#D5E0D8] bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#D5E0D8] pb-4">
                    <div>
                        <p class="text-sm font-extrabold text-[#1E2B24]">{{ $order->order_number }}</p>
                        <p class="text-xs text-[#5A6B61]">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-[#5A6B61]">Total</p>
                        <p class="text-sm font-extrabold text-[#4F7A63]">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                    @php
                        $statusStyles = [
                            'menunggu_pembayaran' => 'bg-amber-100 text-amber-700',
                            'diproses' => 'bg-[#4F7A63]/10 text-[#4F7A63]',
                            'dikirim' => 'bg-indigo-100 text-indigo-700',
                            'selesai' => 'bg-green-100 text-green-700',
                            'dibatalkan' => 'bg-red-100 text-[#E53935]',
                        ];
                    @endphp
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusStyles[$order->status] ?? 'bg-neutral-100 text-neutral-700' }}">
                        {{ \App\Models\Order::statusLabel($order->status) }}
                    </span>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <div class="flex -space-x-3">
                        @foreach ($order->items->take(4) as $item)
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}"
                                class="h-14 w-14 rounded-xl border-2 border-white object-cover shadow-sm">
                        @endforeach
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-[#1E2B24]">
                            {{ $order->items->first()->product_name ?? 'Pesanan' }}
                            @if ($order->items->count() > 1)
                                <span class="text-[#5A6B61]">+{{ $order->items->count() - 1 }} produk lainnya</span>
                            @endif
                        </p>
                        <p class="text-xs text-[#5A6B61]">{{ $order->items->sum('quantity') }} barang &middot; {{ $order->courier }} {{ $order->shipping_service }}</p>
                    </div>
                    <a href="{{ route('order.show', $order->order_number) }}"
                        class="rounded-xl border border-[#D5E0D8] bg-white px-5 py-2.5 text-sm font-bold text-[#4F7A63] transition hover:bg-[#F6F9F7]">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-[#D5E0D8] bg-white py-20 text-center shadow-sm">
                <x-icon name="clipboard" class="mx-auto h-14 w-14 text-[#5A6B61]" />
                <h2 class="mt-4 text-xl font-bold text-[#1E2B24]">Belum ada pesanan</h2>
                <p class="mt-1 text-sm text-[#5A6B61]">Riwayat pesanan kamu akan muncul di sini.</p>
                <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-xl bg-[#4F7A63] px-7 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                    Belanja Sekarang
                </a>
            </div>
        @endforelse
    </div>
@endsection
