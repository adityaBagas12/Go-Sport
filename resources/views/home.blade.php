@extends('layouts.app')

@section('title', 'GoSport - Lengkapi Perlengkapan Olahragamu')

@section('content')
    {{-- Hero Section (Sekunder #2F4F3E dengan Aksen Primer #4F7A63 & Sage Muda #DCE8DF) --}}
    <section class="relative overflow-hidden bg-[#2F4F3E] text-white">
        <div class="absolute inset-0 opacity-15" style="background-image: linear-gradient(115deg, transparent 0 45%, #4F7A63 45% 47%, transparent 47%), linear-gradient(115deg, transparent 0 60%, #4F7A63 60% 62%, transparent 62%);"></div>
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-[#4F7A63]/25 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-[#4F7A63]/25 blur-3xl"></div>

        <div class="container-page relative grid items-center gap-12 py-16 lg:grid-cols-2 lg:py-24">
            <div class="animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white">
                    <x-icon name="bolt" class="h-4 w-4 text-[#DCE8DF]" /> Toko Olahraga #1 di Indonesia
                </span>
                <h1 class="mt-5 text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Lengkapi Perlengkapan <span class="text-[#DCE8DF]">Olahragamu</span> Bersama GoSport
                </h1>
                <p class="mt-5 max-w-lg text-lg text-emerald-100/90">
                    Temukan produk olahraga berkualitas original dengan harga terbaik untuk pria, wanita, dan anak.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-[#4F7A63] px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-[#4F7A63]/30 transition hover:-translate-y-0.5 hover:bg-[#3E6350]">
                        Belanja Sekarang <x-icon name="arrow-right" class="h-5 w-5" />
                    </a>
                    <a href="{{ route('categories.index') }}"
                        class="inline-flex items-center gap-2 rounded-full border-2 border-white/70 px-7 py-3.5 text-sm font-bold text-white transition hover:bg-white hover:text-[#2F4F3E]">
                        Lihat Kategori
                    </a>
                </div>

                <dl class="mt-10 grid max-w-md grid-cols-3 gap-6">
                    <div>
                        <dt class="text-2xl font-black text-[#DCE8DF]">1.500+</dt>
                        <dd class="text-xs text-emerald-200/70">Produk Original</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-black text-[#DCE8DF]">50rb+</dt>
                        <dd class="text-xs text-emerald-200/70">Pelanggan Puas</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-black text-[#DCE8DF]">4.9/5</dt>
                        <dd class="text-xs text-emerald-200/70">Rating Toko</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="border-t border-white/10 bg-[#243F31]">
            <div class="container-page grid grid-cols-2 gap-6 py-6 lg:grid-cols-4">
                @foreach ([
                    ['truck', 'Pengiriman Cepat', 'Ke seluruh Indonesia'],
                    ['shield', '100% Original', 'Produk bergaransi'],
                    ['refresh', 'Retur 7 Hari', 'Tanpa ribet'],
                    ['headset', 'CS 24/7', 'Siap membantu'],
                ] as [$icon, $title, $subtitle])
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/10 text-[#DCE8DF]">
                            <x-icon :name="$icon" class="h-6 w-6" />
                        </span>
                        <div>
                            <p class="text-sm font-bold text-white">{{ $title }}</p>
                            <p class="text-xs text-emerald-200/70">{{ $subtitle }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Kategori (Permukaan Putih #FFFFFF) --}}
    <section class="container-page py-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-[#4F7A63]">Kategori Olahraga</p>
                <h2 class="mt-1 text-2xl font-extrabold text-[#1E2B24] sm:text-3xl">Pilih Sesuai Cabangmu</h2>
            </div>
            <a href="{{ route('categories.index') }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-bold text-[#4F7A63] hover:underline sm:flex">
                Semua Kategori <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}"
                    class="group relative overflow-hidden rounded-2xl border border-[#D5E0D8] bg-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#4F7A63] hover:shadow-lg">
                    <div class="aspect-[4/3] overflow-hidden rounded-xl bg-[#F6F9F7]">
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" loading="lazy"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xl">{{ $category->icon }}</span>
                        <div>
                            <p class="text-sm font-bold text-[#1E2B24] transition group-hover:text-[#4F7A63]">{{ $category->name }}</p>
                            <p class="text-xs text-[#5A6B61]">{{ $category->products_count }} produk</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Section Selang-Seling (Permukaan Sage Muda #DCE8DF / #F6F9F7) --}}
    <section class="border-y border-[#D5E0D8] bg-[#DCE8DF]/35 py-16">
        <div class="container-page">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wider text-[#4F7A63]">Produk Terbaru</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-[#1E2B24] sm:text-3xl">Baru Rilis</h2>
                </div>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-bold text-[#4F7A63] hover:underline sm:flex">
                    Lihat Semua <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($latestProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Banner Promo (Sekunder #2F4F3E & Primer #4F7A63) --}}
    <section class="container-page py-16">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#2F4F3E] via-[#3E6350] to-[#2F4F3E] border border-[#243F31] px-8 py-12 text-white shadow-xl sm:px-14">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 30%, #fff 1px, transparent 1px), radial-gradient(circle at 70% 60%, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
                <div class="max-w-xl">
                    <p class="inline-flex items-center gap-2 rounded-full bg-[#4F7A63] px-3.5 py-1 text-xs font-black uppercase tracking-wider text-white">
                        <x-icon name="sparkles" class="h-4 w-4 text-white" /> Promo Spesial Bulan Ini
                    </p>
                    <h2 class="mt-4 text-3xl font-black leading-tight text-white sm:text-4xl">Diskon Hingga 40% untuk Perlengkapan Favoritmu</h2>
                    <p class="mt-3 text-emerald-100/90">Gunakan voucher <span class="font-extrabold text-[#DCE8DF]">GOSPORT10</span> dan dapatkan gratis ongkir untuk pembelian minimal Rp 300.000.</p>
                </div>
                <a href="{{ route('pages.promo') }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-full bg-[#4F7A63] px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:bg-[#3E6350]">
                    Ambil Promo <x-icon name="arrow-right" class="h-5 w-5 text-white" />
                </a>
            </div>
        </div>
    </section>

    {{-- Produk Terlaris (Permukaan Putih #FFFFFF) --}}
    <section class="container-page pb-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-[#4F7A63]">Produk Terlaris</p>
                <h2 class="mt-1 text-2xl font-extrabold text-[#1E2B24] sm:text-3xl">Paling Banyak Dibeli</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'best']) }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-bold text-[#4F7A63] hover:underline sm:flex">
                Lihat Semua <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @foreach ($bestSellers as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    {{-- Promo & Diskon (Section Selang-Seling #DCE8DF / #F6F9F7) --}}
    <section class="border-y border-[#D5E0D8] bg-[#DCE8DF]/35 py-16">
        <div class="container-page">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wider text-[#4F7A63]">Promo & Diskon</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-[#1E2B24] sm:text-3xl">Harga Spesial Hari Ini</h2>
                </div>
                <a href="{{ route('pages.promo') }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-bold text-[#4F7A63] hover:underline sm:flex">
                    Lihat Semua <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($promoProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Newsletter (Permukaan Kartu #FFFFFF dengan Border #D5E0D8) --}}
    <section class="container-page py-20">
        <div class="grid gap-6 rounded-3xl border border-[#D5E0D8] bg-white p-8 shadow-sm lg:grid-cols-[1.2fr_1fr] lg:p-12">
            <div>
                <h2 class="text-2xl font-extrabold text-[#1E2B24] sm:text-3xl">Dapatkan Info Promo & Produk Baru</h2>
                <p class="mt-3 max-w-lg text-[#5A6B61]">
                    Berlangganan newsletter GoSport dan jadilah yang pertama tahu tentang diskon, produk baru, dan tips olahraga.
                </p>
            </div>
            <form class="flex flex-col gap-3 sm:flex-row" onsubmit="event.preventDefault(); this.reset(); alert('Terima kasih telah berlangganan newsletter GoSport!');">
                <input type="email" required placeholder="Alamat email kamu"
                    class="w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
                <button type="submit" class="shrink-0 rounded-xl bg-[#4F7A63] px-7 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                    Berlangganan
                </button>
            </form>
        </div>
    </section>
@endsection
