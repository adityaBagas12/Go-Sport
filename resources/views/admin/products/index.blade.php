@extends('layouts.admin')

@section('title', 'Kelola Produk - GoSport')
@section('admin-title', 'Kelola Produk')

@section('content')
    <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-1 flex-wrap items-center gap-3">
                <div class="relative flex min-w-[220px] flex-1 items-center">
                    <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-neutral-400" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                        class="w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] py-2.5 pl-10 pr-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                </div>
                <select name="category" data-auto-submit class="rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-3 py-2.5 text-sm outline-none focus:border-[#0A6CFF]">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-[#0A2A5E] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#0A6CFF]">Cari</button>
            </form>

            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-[#0A6CFF] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3D9BFF]">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Produk
            </a>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[860px] text-sm">
                <thead>
                    <tr class="border-b border-[#8CC4FF]/30 text-left text-xs uppercase tracking-wide text-neutral-500">
                        <th class="pb-3 pr-4 font-semibold">Produk</th>
                        <th class="pb-3 pr-4 font-semibold">Kategori</th>
                        <th class="pb-3 pr-4 font-semibold">Harga</th>
                        <th class="pb-3 pr-4 font-semibold">Stok</th>
                        <th class="pb-3 pr-4 font-semibold">Terjual</th>
                        <th class="pb-3 pr-4 font-semibold">Status</th>
                        <th class="pb-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#8CC4FF]/20">
                    @forelse ($products as $product)
                        <tr class="hover:bg-[#F4F8FF]">
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-xl object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-[#10213F]">{{ $product->name }}</p>
                                        @if ($product->badge)
                                            <span class="text-xs font-bold text-[#FF7A00]">{{ ucfirst($product->badge) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 pr-4 text-neutral-600">{{ $product->category->name ?? '-' }}</td>
                            <td class="py-3 pr-4">
                                <p class="font-semibold text-[#10213F]">{{ $product->price_label }}</p>
                                @if ($product->is_discounted)
                                    <p class="text-xs text-neutral-400 line-through">{{ 'Rp ' . number_format($product->compare_price, 0, ',', '.') }}</p>
                                @endif
                            </td>
                            <td class="py-3 pr-4">
                                <span class="font-semibold {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-amber-600' : 'text-[#E53935]') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-neutral-600">{{ number_format($product->sold, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-neutral-100 text-neutral-500' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="grid h-9 w-9 place-items-center rounded-lg border border-[#8CC4FF] text-[#0A6CFF] hover:bg-[#E6F1FF]" title="Edit">
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
                        <tr><td colspan="7" class="py-12 text-center text-neutral-400">Tidak ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $products->links() }}</div>
    </div>
@endsection
