@extends('layouts.admin')

@section('title', 'Kelola Voucher - GoSport')
@section('admin-title', 'Voucher & Promo')

@section('content')
    <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Daftar Voucher</h2>
            <a href="{{ route('admin.coupons.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-[#4F7A63] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Voucher
            </a>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead>
                    <tr class="border-b border-[#D5E0D8] text-left text-xs uppercase tracking-wide text-[#5A6B61]">
                        <th class="pb-3 pr-4 font-semibold">Kode</th>
                        <th class="pb-3 pr-4 font-semibold">Tipe</th>
                        <th class="pb-3 pr-4 font-semibold">Nilai</th>
                        <th class="pb-3 pr-4 font-semibold">Min. Belanja</th>
                        <th class="pb-3 pr-4 font-semibold">Status</th>
                        <th class="pb-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D5E0D8]">
                    @forelse ($coupons as $coupon)
                        <tr class="hover:bg-[#F6F9F7]">
                            <td class="py-3 pr-4">
                                <span class="rounded-lg border border-[#4F7A63]/30 bg-[#4F7A63]/10 px-3 py-1 font-mono text-sm font-bold text-[#4F7A63]">{{ $coupon->code }}</span>
                            </td>
                            <td class="py-3 pr-4 text-[#5A6B61]">{{ $coupon->type === 'percent' ? 'Persentase' : 'Nominal' }}</td>
                            <td class="py-3 pr-4 font-semibold text-[#1E2B24]">{{ $coupon->label }}</td>
                            <td class="py-3 pr-4 text-[#5A6B61]">{{ 'Rp ' . number_format($coupon->min_order, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $coupon->is_active ? 'bg-green-100 text-green-700' : 'bg-neutral-100 text-neutral-500' }}">
                                    {{ $coupon->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-[#D5E0D8] text-[#4F7A63] hover:bg-[#F6F9F7]">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Hapus voucher ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="grid h-9 w-9 place-items-center rounded-lg border border-[#E53935]/40 text-[#E53935] hover:bg-[#E53935]/10">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-[#5A6B61]">Belum ada voucher.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
