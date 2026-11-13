@extends('layouts.account')

@section('account-title', 'Riwayat Pesanan')
@section('title', 'Riwayat Pesanan - GoSport')

@section('account-content')
    <div class="space-y-4">
        @forelse ($orders as $order)
            <div class="rounded-3xl border border-[#8CC4FF] bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#8CC4FF]/30 pb-4">
                    <div>
                        <p class="text-sm font-extrabold text-[#10213F]">{{ $order->order_number }}</p>
                        <p class="text-xs text-neutral-500">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-neutral-500">Total</p>
                        <p class="text-sm font-extrabold text-[#0A6CFF]">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                    @php
                        $statusStyles = [
                            'menunggu_pembayaran' => 'bg-amber-100 text-amber-700',
                            'diproses' => 'bg-blue-100 text-[#0A6CFF]',
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
                        <p class="truncate text-sm font-semibold text-[#10213F]">
                            {{ $order->items->first()->product_name ?? 'Pesanan' }}
                            @if ($order->items->count() > 1)
                                <span class="text-neutral-400">+{{ $order->items->count() - 1 }} produk lainnya</span>
                            @endif
                        </p>
                        <p class="text-xs text-neutral-500">{{ $order->items->sum('quantity') }} barang &middot; {{ $order->courier }} {{ $order->shipping_service }}</p>
                    </div>
                    <a href="{{ route('order.show', $order->order_number) }}"
                        class="rounded-xl border border-[#8CC4FF] bg-white px-5 py-2.5 text-sm font-bold text-[#0A6CFF] transition hover:bg-[#E6F1FF]">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-[#8CC4FF] bg-white py-20 text-center shadow-sm">
                <x-icon name="clipboard" class="mx-auto h-14 w-14 text-[#8CC4FF]" />
                <h2 class="mt-4 text-xl font-bold text-[#0A2A5E]">Belum ada pesanan</h2>
                <p class="mt-1 text-sm text-neutral-500">Riwayat pesanan kamu akan muncul di sini.</p>
                <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-xl bg-[#FF7A00] px-7 py-3.5 text-sm font-bold text-[#10213F] shadow-sm transition hover:brightness-105">
                    Mulai Belanja
                </a>
            </div>
        @endforelse

        <div class="mt-8">{{ $orders->links() }}</div>
    </div>
@endsection
