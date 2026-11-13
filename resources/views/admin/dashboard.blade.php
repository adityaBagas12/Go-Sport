@extends('layouts.admin')
@section('admin-title', 'Dashboard')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        @foreach ([
            ['box', 'Total Produk', number_format($totalProducts, 0, ',', '.'), 'bg-blue-50 text-blue-600'],
            ['tag', 'Total Kategori', number_format($totalCategories, 0, ',', '.'), 'bg-purple-50 text-purple-600'],
            ['users', 'Total Customer', number_format($totalCustomers, 0, ',', '.'), 'bg-emerald-50 text-emerald-600'],
            ['clipboard', 'Total Pesanan', number_format($totalOrders, 0, ',', '.'), 'bg-amber-50 text-amber-600'],
            ['check-circle', 'Pesanan Dibayar', number_format($paidOrders, 0, ',', '.'), 'bg-green-50 text-green-600'],
            ['clock', 'Menunggu Bayar', number_format($pendingOrders, 0, ',', '.'), 'bg-rose-50 text-rose-600'],
        ] as [$icon, $label, $value, $color])
            <div class="rounded-3xl border border-neutral-100 bg-white p-5 shadow-sm">
                <span class="grid h-11 w-11 place-items-center rounded-2xl {{ $color }}">
                    <x-icon :name="$icon" class="h-6 w-6" />
                </span>
                <p class="mt-4 text-xs font-medium uppercase tracking-wide text-neutral-400">{{ $label }}</p>
                <p class="mt-1 text-xl font-black text-neutral-900">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    @if ($pendingOrders > 0)
        <a href="{{ route('admin.orders.index', ['status' => 'menunggu_pembayaran']) }}"
            class="mt-6 flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 transition hover:bg-amber-100">
            <x-icon name="clock" class="h-6 w-6 text-amber-600" />
            <p class="text-sm font-semibold text-amber-800">
                Ada <span class="font-black">{{ $pendingOrders }}</span> pesanan menunggu pembayaran. Klik untuk melihat.
            </p>
        </a>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.5fr_1fr]">
        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-neutral-900">Grafik Penjualan</h2>
                    <p class="text-sm text-neutral-500">{{ $chart['subtitle'] }}</p>
                </div>
                <span class="rounded-xl bg-brand-50 px-4 py-2 text-sm font-bold text-brand-600">
                    {{ $chart['total_label'] }}
                </span>
            </div>

            @include('admin.partials.sales-chart', ['chart' => $chart])
        </div>

        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-neutral-900">Produk Terlaris</h2>
            <div class="mt-4 space-y-3">
                @foreach ($bestSellers as $index => $product)
                    <div class="flex items-center gap-3">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-neutral-900 text-xs font-bold text-white">{{ $index + 1 }}</span>
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-12 w-12 shrink-0 rounded-xl object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-neutral-900">{{ $product->name }}</p>
                            <p class="text-xs text-neutral-500">{{ number_format($product->sold, 0, ',', '.') }} terjual</p>
                        </div>
                        <p class="text-sm font-bold text-brand-600">{{ $product->price_label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-neutral-900">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Lihat semua</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 text-left text-xs uppercase tracking-wide text-neutral-400">
                        <th class="pb-3 pr-4 font-semibold">No. Pesanan</th>
                        <th class="pb-3 pr-4 font-semibold">Customer</th>
                        <th class="pb-3 pr-4 font-semibold">Tanggal</th>
                        <th class="pb-3 pr-4 font-semibold">Total</th>
                        <th class="pb-3 pr-4 font-semibold">Status</th>
                        <th class="pb-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($latestOrders as $order)
                        <tr class="hover:bg-neutral-50">
                            <td class="py-3 pr-4 font-bold text-neutral-900">{{ $order->order_number }}</td>
                            <td class="py-3 pr-4 text-neutral-600">{{ $order->user->name ?? '-' }}</td>
                            <td class="py-3 pr-4 text-neutral-500">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                            <td class="py-3 pr-4 font-semibold text-neutral-900">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-700">
                                    {{ \App\Models\Order::statusLabel($order->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-bold text-brand-600 hover:underline">Detail</a>
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
