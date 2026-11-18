@extends('layouts.admin')

@section('title', 'Kelola Produk - GoSport')
@section('admin-title', 'Kelola Produk')

@section('content')
    <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-1 flex-wrap items-center gap-3">
                <div class="relative flex min-w-[220px] flex-1 items-center">
                    <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-[#5A6B61]" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                        class="w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] py-2.5 pl-10 pr-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <select name="category" data-auto-submit class="rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-2.5 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63]">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-[#2F4F3E] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#243F31]">Cari</button>
            </form>

            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-[#4F7A63] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Produk
            </a>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[860px] text-sm">
                <thead>
                    <tr class="border-b border-[#D5E0D8] text-left text-xs uppercase tracking-wide text-[#5A6B61]">
                        <th class="pb-3 pr-4 font-semibold">Produk</th>
                        <th class="pb-3 pr-4 font-semibold">Kategori</th>
                        <th class="pb-3 pr-4 font-semibold">Harga</th>
                        <th class="pb-3 pr-4 font-semibold">Stok</th>
                        <th class="pb-3 pr-4 font-semibold">Terjual</th>
                        <th class="pb-3 pr-4 font-semibold">Status</th>
                        <th class="pb-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D5E0D8]">
                    @forelse ($products as $product)
                        <tr class="hover:bg-[#F6F9F7]">
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-xl object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-[#1E2B24]">{{ $product->name }}</p>
                                        @if ($product->badge)
                                            <span class="text-xs font-bold text-[#4F7A63]">{{ ucfirst($product->badge) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 pr-4 text-[#5A6B61]">{{ $product->category->name ?? '-' }}</td>
                            <td class="py-3 pr-4">
                                <p class="font-semibold text-[#1E2B24]">{{ $product->price_label }}</p>
                                @if ($product->is_discounted)
                                    <p class="text-xs text-[#5A6B61] line-through">{{ 'Rp ' . number_format($product->compare_price, 0, ',', '.') }}</p>
                                @endif
                            </td>
                            <td class="py-3 pr-4">
                                <span class="font-semibold {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-amber-600' : 'text-[#E53935]') }}">
                                    {{ $product->stock }}
                                </span>
                                @if ($product->variants->isNotEmpty())
                                    <span class="block text-[11px] text-[#5A6B61]">({{ $product->variants->count() }} varian)</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-[#5A6B61]">{{ number_format($product->sold, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-neutral-100 text-neutral-500' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-[#D5E0D8] text-[#4F7A63] hover:bg-[#F6F9F7]" title="Edit">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="grid h-9 w-9 place-items-center rounded-lg border border-[#E53935]/40 text-[#E53935] hover:bg-[#E53935]/10" title="Hapus">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-[#5A6B61]">Tidak ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $products->links() }}</div>
    </div>
@endsection
