@extends('layouts.admin')

@section('title', 'Data Customer - GoSport')
@section('admin-title', 'Data Customer')

@section('content')
    <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex min-w-[220px] flex-1 items-center">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-[#5A6B61]" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                    class="w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] py-2.5 pl-10 pr-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
            </div>
            <button type="submit" class="rounded-xl bg-[#2F4F3E] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#243F31]">Cari</button>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead>
                    <tr class="border-b border-[#D5E0D8] text-left text-xs uppercase tracking-wide text-[#5A6B61]">
                        <th class="pb-3 pr-4 font-semibold">Nama</th>
                        <th class="pb-3 pr-4 font-semibold">Email</th>
                        <th class="pb-3 pr-4 font-semibold">Telepon</th>
                        <th class="pb-3 pr-4 font-semibold">Total Pesanan</th>
                        <th class="pb-3 pr-4 font-semibold">Bergabung</th>
                        <th class="pb-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D5E0D8]">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-[#F6F9F7]">
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 place-items-center rounded-full bg-[#2F4F3E] text-sm font-bold text-white shadow-sm">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </span>
                                    <span class="font-semibold text-[#1E2B24]">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 pr-4 text-[#5A6B61]">{{ $customer->email }}</td>
                            <td class="py-3 pr-4 text-[#5A6B61]">{{ $customer->phone ?? '-' }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-1 text-xs font-bold text-[#4F7A63]">{{ $customer->orders_count }} pesanan</span>
                            </td>
                            <td class="py-3 pr-4 text-[#5A6B61]">{{ $customer->created_at->translatedFormat('d M Y') }}</td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="text-xs font-bold text-[#4F7A63] hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-[#5A6B61]">Belum ada customer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $customers->links() }}</div>
    </div>
@endsection
