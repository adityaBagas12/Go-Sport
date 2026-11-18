@extends('layouts.app')

@section('title', 'Katalog Produk - GoSport')

@section('content')
    <div class="border-b border-[#D5E0D8] bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-[#4F7A63]">
                <a href="{{ route('home') }}" class="hover:underline">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4 text-[#5A6B61]" />
                <span class="font-bold text-[#1E2B24]">Produk</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-[#1E2B24]">
                {{ $selectedCategory->name ?? 'Semua Produk' }}
            </h1>
            <p class="mt-1 text-sm text-[#5A6B61]">
                {{ $products->total() }} produk ditemukan
                @if (request('search'))
                    untuk pencarian "<span class="font-bold text-[#1E2B24]">{{ request('search') }}</span>"
                @endif
            </p>
        </div>
    </div>

    <div class="container-page grid gap-8 py-10 lg:grid-cols-[280px_1fr]">
        <aside class="space-y-6">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-6">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="rounded-2xl border border-[#D5E0D8] bg-white p-5 shadow-sm">
                    <div class="relative flex items-center">
                        <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-[#5A6B61]" />
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                            class="w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] py-2.5 pl-10 pr-3 text-sm text-[#1E2B24] outline-none transition focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
                    </div>
                </div>

                <div class="rounded-2xl border border-[#D5E0D8] bg-white p-5 shadow-sm">
                    <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-[#1E2B24]">
                        <x-icon name="grid" class="h-4 w-4 text-[#4F7A63]" /> Kategori
                    </h3>
                    <div class="mt-4 space-y-1">
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 text-sm text-[#1E2B24] hover:bg-[#F6F9F7]">
                            <input type="radio" name="category" value="" @checked(! request('category')) class="h-4 w-4 text-[#4F7A63] focus:ring-[#4F7A63]">
                            <span class="flex-1 font-medium">Semua Kategori</span>
                        </label>
                        @foreach ($categories as $category)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 text-sm text-[#1E2B24] hover:bg-[#F6F9F7]">
                                <input type="radio" name="category" value="{{ $category->id }}" @checked(request('category') == $category->id) class="h-4 w-4 text-[#4F7A63] focus:ring-[#4F7A63]">
                                <span class="flex-1 font-medium">{{ $category->name }}</span>
                                <span class="text-xs text-[#5A6B61]">{{ $category->products_count }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-[#D5E0D8] bg-white p-5 shadow-sm">
                    <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-[#1E2B24]">
                        <x-icon name="wallet" class="h-4 w-4 text-[#4F7A63]" /> Rentang Harga
                    </h3>
                    <div class="mt-4 space-y-3">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Harga minimum" min="0"
                            class="w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-2.5 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Harga maksimum" min="0"
                            class="w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-2.5 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
                    </div>
                </div>

                <div class="rounded-2xl border border-[#D5E0D8] bg-white p-5 shadow-sm">
                    <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-[#1E2B24]">
                        <x-icon name="tag" class="h-4 w-4 text-[#4F7A63]" /> Label
                    </h3>
                    <div class="mt-4 space-y-1">
                        @foreach (['' => 'Semua', 'baru' => 'Baru', 'terlaris' => 'Terlaris', 'promo' => 'Promo'] as $value => $label)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 text-sm text-[#1E2B24] hover:bg-[#F6F9F7]">
                                <input type="radio" name="badge" value="{{ $value }}" @checked(request('badge', '') === $value) class="h-4 w-4 text-[#4F7A63] focus:ring-[#4F7A63]">
                                <span class="font-medium">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 rounded-xl bg-[#4F7A63] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#3E6350]">
                        Terapkan
                    </button>
                    <a href="{{ route('products.index') }}" class="grid place-items-center rounded-xl border border-[#D5E0D8] px-4 text-sm font-semibold text-[#1E2B24] transition hover:bg-[#F6F9F7]">
                        Reset
                    </a>
                </div>
            </form>
        </aside>

        <div>
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-[#D5E0D8] bg-white px-5 py-3.5 shadow-sm">
                <p class="text-sm text-[#5A6B61]">
                    Menampilkan <span class="font-bold text-[#1E2B24]">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-[#1E2B24]">{{ $products->total() }}</span> produk
                </p>
                <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
                    @foreach (['search', 'category', 'badge', 'min_price', 'max_price'] as $param)
                        @if (request($param))
                            <input type="hidden" name="{{ $param }}" value="{{ request($param) }}">
                        @endif
                    @endforeach
                    <label class="text-sm font-medium text-[#1E2B24]">Urutkan</label>
                    <select name="sort" data-auto-submit class="rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-2 text-sm font-semibold text-[#1E2B24] outline-none focus:border-[#4F7A63]">
                        <option value="best" @selected(request('sort') === 'best')>Terlaris</option>
                        <option value="newest" @selected(request('sort') === 'newest')>Terbaru</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Harga Termurah</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Harga Termahal</option>
                    </select>
                </form>
            </div>

            @if ($products->count())
                <div class="grid grid-cols-2 gap-4 sm:gap-6 xl:grid-cols-3">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-[#D5E0D8] bg-white py-20 text-center">
                    <x-icon name="search" class="mx-auto h-12 w-12 text-[#5A6B61]" />
                    <h3 class="mt-4 text-lg font-bold text-[#1E2B24]">Produk tidak ditemukan</h3>
                    <p class="mt-1 text-sm text-[#5A6B61]">Coba ubah kata kunci atau filter pencarian kamu.</p>
                    <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-xl bg-[#4F7A63] px-6 py-3 text-sm font-bold text-white hover:bg-[#3E6350]">
                        Lihat Semua Produk
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
