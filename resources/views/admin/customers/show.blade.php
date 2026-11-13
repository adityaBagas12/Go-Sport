@extends('layouts.admin')

@section('title', 'Detail Customer - GoSport')
@section('admin-title', 'Detail Customer')

@section('content')
    <a href="{{ route('admin.customers.index') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-[#0A6CFF] hover:underline">
        <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke data customer
    </a>

    <div class="grid gap-6 lg:grid-cols-[1fr_1.6fr]">
        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <span class="grid h-16 w-16 place-items-center rounded-2xl bg-[#0A2A5E] text-2xl font-black text-white shadow-sm">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </span>
                <div>
                    <p class="text-lg font-extrabold text-[#10213F]">{{ $customer->name }}</p>
                    <p class="text-sm text-neutral-500">Customer sejak {{ $customer->created_at->translatedFormat('F Y') }}</p>
                </div>
            </div>

            <dl class="mt-6 space-y-3 border-t border-[#8CC4FF]/30 pt-5 text-sm">
                <div class="flex justify-between"><dt class="text-neutral-500">Email</dt><dd class="font-semibold text-[#10213F]">{{ $customer->email }}</dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">Telepon</dt><dd class="font-semibold text-[#10213F]">{{ $customer->phone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">Total Pesanan</dt><dd class="font-semibold text-[#10213F]">{{ $customer->orders->count() }}</dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">Total Belanja</dt><dd class="font-bold text-[#0A6CFF]">{{ 'Rp ' . number_format($customer->orders->where('status', '!=', 'dibatalkan')->sum('total'), 0, ',', '.') }}</dd></div>
            </dl>
        </div>

        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#0A2A5E]">Riwayat Pesanan</h2>
            <div class="mt-4 divide-y divide-[#8CC4FF]/20">
                @forelse ($customer->orders->sortByDesc('created_at') as $order)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-4">
                        <div>
                            <p class="text-sm font-bold text-[#10213F]">{{ $order->order_number }}</p>
                            <p class="text-xs text-neutral-500">{{ $order->created_at->translatedFormat('d M Y, H:i') }} &middot; {{ $order->items->count() }} item</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-extrabold text-[#10213F]">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</p>
                            <span class="text-xs font-semibold text-[#0A6CFF]">{{ \App\Models\Order::statusLabel($order->status) }}</span>
                        </div>
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-bold text-[#0A6CFF] hover:underline">Detail</a>
                    </div>
                @empty
                    <p class="py-10 text-center text-sm text-neutral-400">Customer belum melakukan pesanan.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection