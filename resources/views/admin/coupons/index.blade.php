@extends('layouts.admin')

@section('title', 'Kelola Voucher - GoSport')
@section('admin-title', 'Voucher & Promo')

@section('content')
    <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-[#0A2A5E]">Daftar Voucher</h2>
            <a href="{{ route('admin.coupons.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-[#0A6CFF] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3D9BFF]">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Voucher
            </a>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead>
                    <tr class="border-b border-[#8CC4FF]/30 text-left text-xs uppercase tracking-wide text-neutral-500">
                        <th class="pb-3 pr-4 font-semibold">Kode</th>
                        <th class="pb-3 pr-4 font-semibold">Tipe</th>
                        <th class="pb-3 pr-4 font-semibold">Nilai</th>
                        <th class="pb-3 pr-4 font-semibold">Min. Belanja</th>
                        <th class="pb-3 pr-4 font-semibold">Status</th>
                        <th class="pb-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#8CC4FF]/20">
                    @forelse ($coupons as $coupon)
                        <tr class="hover:bg-[#F4F8FF]">
                            <td class="py-3 pr-4">
                                <span class="rounded-lg border border-[#8CC4FF]/60 bg-[#E6F1FF] px-3 py-1 font-mono text-sm font-bold text-[#0A6CFF]">{{ $coupon->code }}</span>
                            </td>
                            <td class="py-3 pr-4 text-neutral-600">{{ $coupon->type === 'percent' ? 'Persentase' : 'Nominal' }}</td>
                            <td class="py-3 pr-4 font-semibold text-[#10213F]">{{ $coupon->label }}</td>
                            <td class="py-3 pr-4 text-neutral-600">{{ 'Rp ' . number_format($coupon->min_order, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $coupon->is_active ? 'bg-green-100 text-green-700' : 'bg-neutral-100 text-neutral-500' }}">
                                    {{ $coupon->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-[#8CC4FF] text-[#0A6CFF] hover:bg-[#E6F1FF]">
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
                        <tr><td colspan="6" class="py-12 text-center text-neutral-400">Belum ada voucher.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
