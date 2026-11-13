@extends('layouts.admin')

@section('title', 'Data Customer - GoSport')
@section('admin-title', 'Data Customer')

@section('content')
    <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex min-w-[220px] flex-1 items-center">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-neutral-400" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                    class="w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] py-2.5 pl-10 pr-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
            </div>
            <button type="submit" class="rounded-xl bg-[#0A2A5E] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#0A6CFF]">Cari</button>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead>
                    <tr class="border-b border-[#8CC4FF]/30 text-left text-xs uppercase tracking-wide text-neutral-500">
                        <th class="pb-3 pr-4 font-semibold">Nama</th>
                        <th class="pb-3 pr-4 font-semibold">Email</th>
                        <th class="pb-3 pr-4 font-semibold">Telepon</th>
                        <th class="pb-3 pr-4 font-semibold">Total Pesanan</th>
                        <th class="pb-3 pr-4 font-semibold">Bergabung</th>
                        <th class="pb-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#8CC4FF]/20">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-[#F4F8FF]">
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 place-items-center rounded-full bg-[#0A2A5E] text-sm font-bold text-white shadow-sm">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </span>
                                    <span class="font-semibold text-[#10213F]">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 pr-4 text-neutral-600">{{ $customer->email }}</td>
                            <td class="py-3 pr-4 text-neutral-600">{{ $customer->phone ?? '-' }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full border border-[#8CC4FF]/50 bg-[#E6F1FF] px-3 py-1 text-xs font-bold text-[#0A6CFF]">{{ $customer->orders_count }} pesanan</span>
                            </td>
                            <td class="py-3 pr-4 text-neutral-500">{{ $customer->created_at->translatedFormat('d M Y') }}</td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="text-xs font-bold text-[#0A6CFF] hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-neutral-400">Belum ada customer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $customers->links() }}</div>
    </div>
@endsection