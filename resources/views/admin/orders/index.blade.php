@extends('layouts.admin')

@section('title', 'Kelola Pesanan - GoSport')
@section('admin-title', 'Kelola Pesanan')

@section('content')
    <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex min-w-[220px] flex-1 items-center">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-neutral-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan..."
                    class="w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] py-2.5 pl-10 pr-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
            </div>
            <select name="status" data-auto-submit class="rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-3 py-2.5 text-sm outline-none focus:border-[#0A6CFF]">
                <option value="">Semua Status</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-[#0A2A5E] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#0A6CFF]">Cari</button>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead>
                    <tr class="border-b border-[#8CC4FF]/30 text-left text-xs uppercase tracking-wide text-neutral-500">
                        <th class="pb-3 pr-4 font-semibold">No. Pesanan</th>
                        <th class="pb-3 pr-4 font-semibold">Customer</th>
                        <th class="pb-3 pr-4 font-semibold">Tanggal</th>
                        <th class="pb-3 pr-4 font-semibold">Produk</th>
                        <th class="pb-3 pr-4 font-semibold">Total</th>
                        <th class="pb-3 pr-4 font-semibold">Status</th>
                        <th class="pb-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#8CC4FF]/20">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-[#F4F8FF]">
                            <td class="py-3 pr-4 font-bold text-[#10213F]">{{ $order->order_number }}</td>
                            <td class="py-3 pr-4">
                                <p class="font-medium text-[#10213F]">{{ $order->user->name ?? '-' }}</p>
                                <p class="text-xs text-neutral-500">{{ $order->recipient_phone }}</p>
                            </td>
                            <td class="py-3 pr-4 text-neutral-500">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="py-3 pr-4 text-neutral-600">{{ $order->items()->count() }} item</td>
                            <td class="py-3 pr-4 font-semibold text-[#10213F]">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" data-auto-submit
                                        class="rounded-lg border border-[#8CC4FF]/60 bg-[#F4F8FF] px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-[#0A6CFF]">
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-bold text-[#0A6CFF] hover:underline">Detail</a>
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
