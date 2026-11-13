@extends('layouts.admin')
@section('admin-title', 'Dashboard')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        @foreach ([
            ['box', 'Total Produk', number_format($totalProducts, 0, ',', '.'), 'bg-[#0A6CFF]/10 text-[#0A6CFF]'],
            ['tag', 'Total Kategori', number_format($totalCategories, 0, ',', '.'), 'bg-[#0A2A5E]/10 text-[#0A2A5E]'],
            ['users', 'Total Customer', number_format($totalCustomers, 0, ',', '.'), 'bg-emerald-50 text-emerald-600'],
            ['clipboard', 'Total Pesanan', number_format($totalOrders, 0, ',', '.'), 'bg-[#FF7A00]/15 text-[#FF7A00]'],
            ['check-circle', 'Pesanan Dibayar', number_format($paidOrders, 0, ',', '.'), 'bg-green-50 text-green-600'],
            ['clock', 'Menunggu Bayar', number_format($pendingOrders, 0, ',', '.'), 'bg-[#E53935]/10 text-[#E53935]'],
        ] as [$icon, $label, $value, $color])
            <div class="rounded-3xl border border-[#8CC4FF] bg-white p-5 shadow-sm">
                <span class="grid h-11 w-11 place-items-center rounded-2xl {{ $color }}">
                    <x-icon :name="$icon" class="h-6 w-6" />
                </span>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-neutral-500">{{ $label }}</p>
                <p class="mt-1 text-2xl font-black text-[#10213F]">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    @if ($pendingOrders > 0)
        <a href="{{ route('admin.orders.index', ['status' => 'menunggu_pembayaran']) }}"
            class="mt-6 flex items-center gap-3 rounded-2xl border border-[#8CC4FF] bg-[#E6F1FF] px-5 py-4 transition hover:bg-[#d5e7fc]">
            <x-icon name="clock" class="h-6 w-6 text-[#0A6CFF]" />
            <p class="text-sm font-semibold text-[#10213F]">
                Ada <span class="font-black text-[#0A6CFF]">{{ $pendingOrders }}</span> pesanan menunggu pembayaran. Klik untuk melihat.
            </p>
        </a>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.5fr_1fr]">
        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-[#0A2A5E]">Grafik Penjualan</h2>
                    <p class="text-sm text-neutral-500">{{ $chart['subtitle'] }}</p>
                </div>
                <span class="rounded-xl border border-[#8CC4FF] bg-[#E6F1FF] px-4 py-2 text-sm font-bold text-[#0A6CFF]">
                    {{ $chart['total_label'] }}
                </span>
            </div>

            @include('admin.partials.sales-chart', ['chart' => $chart])
        </div>

        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#0A2A5E]">Produk Terlaris</h2>
            <div class="mt-4 space-y-3">
                @foreach ($bestSellers as $index => $product)
                    <div class="flex items-center gap-3">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#0A2A5E] text-xs font-bold text-white">{{ $index + 1 }}</span>
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-12 w-12 shrink-0 rounded-xl object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-[#10213F]">{{ $product->name }}</p>
                            <p class="text-xs text-neutral-500">{{ number_format($product->sold, 0, ',', '.') }} terjual</p>
                        </div>
                        <p class="text-sm font-bold text-[#0A6CFF]">{{ $product->price_label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-[#0A2A5E]">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-[#0A6CFF] hover:underline">Lihat semua</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead>
                    <tr class="border-b border-[#8CC4FF]/30 text-left text-xs uppercase tracking-wide text-neutral-500">
                        <th class="pb-3 pr-4 font-semibold">No. Pesanan</th>
                        <th class="pb-3 pr-4 font-semibold">Customer</th>
                        <th class="pb-3 pr-4 font-semibold">Tanggal</th>
                        <th class="pb-3 pr-4 font-semibold">Total</th>
                        <th class="pb-3 pr-4 font-semibold">Status</th>
                        <th class="pb-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#8CC4FF]/20">
                    @forelse ($latestOrders as $order)
                        <tr class="hover:bg-[#F4F8FF]">
                            <td class="py-3 pr-4 font-bold text-[#10213F]">{{ $order->order_number }}</td>
                            <td class="py-3 pr-4 text-neutral-600">{{ $order->user->name ?? '-' }}</td>
                            <td class="py-3 pr-4 text-neutral-500">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                            <td class="py-3 pr-4 font-semibold text-[#10213F]">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full border border-[#8CC4FF]/50 bg-[#E6F1FF] px-3 py-1 text-xs font-semibold text-[#0A2A5E]">
                                    {{ \App\Models\Order::statusLabel($order->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-bold text-[#0A6CFF] hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-neutral-400">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
