@extends('layouts.admin')

@section('title', 'Kelola Pesanan - GoSport')
@section('admin-title', 'Kelola Pesanan')

@section('content')
    <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex min-w-[220px] flex-1 items-center">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-neutral-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan..."
                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-2.5 pl-10 pr-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
            </div>
            <select name="status" data-auto-submit class="rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm outline-none focus:border-brand-500">
                <option value="">Semua Status</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-neutral-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-500">Cari</button>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 text-left text-xs uppercase tracking-wide text-neutral-400">
                        <th class="pb-3 pr-4 font-semibold">No. Pesanan</th>
                        <th class="pb-3 pr-4 font-semibold">Customer</th>
                        <th class="pb-3 pr-4 font-semibold">Tanggal</th>
                        <th class="pb-3 pr-4 font-semibold">Produk</th>
                        <th class="pb-3 pr-4 font-semibold">Total</th>
                        <th class="pb-3 pr-4 font-semibold">Status</th>
                        <th class="pb-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-neutral-50">
                            <td class="py-3 pr-4 font-bold text-neutral-900">{{ $order->order_number }}</td>
                            <td class="py-3 pr-4">
                                <p class="font-medium text-neutral-800">{{ $order->user->name ?? '-' }}</p>
                                <p class="text-xs text-neutral-400">{{ $order->recipient_phone }}</p>
                            </td>
                            <td class="py-3 pr-4 text-neutral-500">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="py-3 pr-4 text-neutral-600">{{ $order->items()->count() }} item</td>
                            <td class="py-3 pr-4 font-semibold text-neutral-900">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" data-auto-submit
                                        class="rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-brand-500">
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-bold text-brand-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-neutral-400">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
@endsection
