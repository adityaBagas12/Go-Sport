@extends('layouts.admin')

@section('title', 'Kelola Kategori - GoSport')
@section('admin-title', 'Kelola Kategori')

@section('content')
    <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Daftar Kategori</h2>
            <a href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-[#4F7A63] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                <x-icon name="plus" class="h-4 w-4" /> Tambah Kategori
            </a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($categories as $category)
                <div class="overflow-hidden rounded-2xl border border-[#D5E0D8] bg-white">
                    <div class="relative aspect-[4/3] bg-[#F6F9F7]">
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="h-full w-full object-cover">
                        <span class="absolute left-3 top-3 grid h-10 w-10 place-items-center rounded-xl bg-white/95 text-xl shadow-sm backdrop-blur">{{ $category->icon }}</span>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-[#1E2B24]">{{ $category->name }}</p>
                                <p class="text-xs text-[#5A6B61]">{{ $category->products_count }} produk</p>
                            </div>
                            <div class="flex gap-1.5">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="grid h-8 w-8 place-items-center rounded-lg border border-[#D5E0D8] text-[#4F7A63] hover:bg-[#F6F9F7]">
                                    <x-icon name="pencil" class="h-4 w-4" />
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                    onsubmit="return confirm('{{ $category->products_count > 0 ? "Kategori ini masih memiliki {$category->products_count} produk dan tidak dapat dihapus." : 'Hapus kategori ini?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg border border-[#E53935]/40 text-[#E53935] hover:bg-[#E53935]/10">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="mt-2 line-clamp-2 text-xs text-[#5A6B61]">{{ $category->description }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-[#5A6B61]">Belum ada kategori.</div>
            @endforelse
        </div>
    </div>
@endsection
