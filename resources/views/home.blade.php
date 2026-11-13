@extends('layouts.app')

@section('title', 'GoSport - Lengkapi Perlengkapan Olahragamu')

@section('content')
    <section class="relative overflow-hidden bg-neutral-900 text-white">
        <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(115deg, transparent 0 45%, #f97316 45% 47%, transparent 47%), linear-gradient(115deg, transparent 0 60%, #f97316 60% 62%, transparent 62%);"></div>
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-blue-500/10 blur-3xl"></div>

        <div class="container-page relative grid items-center gap-12 py-16 lg:grid-cols-2 lg:py-24">
            <div class="animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-brand-400">
                    <x-icon name="bolt" class="h-4 w-4" /> Toko Olahraga #1 di Indonesia
                </span>
                <h1 class="mt-5 text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                    Lengkapi Perlengkapan <span class="text-brand-500">Olahragamu</span> Bersama GoSport
                </h1>
                <p class="mt-5 max-w-lg text-lg text-neutral-300">
                    Temukan produk olahraga berkualitas dengan harga terbaik untuk pria, wanita, dan anak.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:-translate-y-0.5 hover:bg-brand-600">
                        Belanja Sekarang <x-icon name="arrow-right" class="h-5 w-5" />
                    </a>
                    <a href="{{ route('categories.index') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 px-7 py-3.5 text-sm font-bold text-white transition hover:bg-white hover:text-neutral-900">
                        Lihat Kategori
                    </a>
                </div>

                <dl class="mt-10 grid max-w-md grid-cols-3 gap-6">
                    <div>
                        <dt class="text-2xl font-extrabold text-brand-500">1.500+</dt>
                        <dd class="text-xs text-neutral-400">Produk Original</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-extrabold text-brand-500">50rb+</dt>
                        <dd class="text-xs text-neutral-400">Pelanggan Puas</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-extrabold text-brand-500">4.9/5</dt>
                        <dd class="text-xs text-neutral-400">Rating Toko</dd>
                    </div>
                </dl>
            </div>

           
        </div>

        <div class="border-t border-white/10 bg-black/20">
            <div class="container-page grid grid-cols-2 gap-6 py-6 lg:grid-cols-4">
                @foreach ([
                    ['truck', 'Pengiriman Cepat', 'Ke seluruh Indonesia'],
                    ['shield', '100% Original', 'Produk bergaransi'],
                    ['refresh', 'Retur 7 Hari', 'Tanpa ribet'],
                    ['headset', 'CS 24/7', 'Siap membantu'],
                ] as [$icon, $title, $subtitle])
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-500/15 text-brand-500">
                            <x-icon :name="$icon" class="h-6 w-6" />
                        </span>
                        <div>
                            <p class="text-sm font-bold">{{ $title }}</p>
                            <p class="text-xs text-neutral-400">{{ $subtitle }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container-page py-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-brand-500">Kategori Olahraga</p>
                <h2 class="mt-1 text-2xl font-extrabold text-neutral-900 sm:text-3xl">Pilih Sesuai Cabangmu</h2>
            </div>
            <a href="{{ route('categories.index') }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-neutral-700 hover:text-brand-600 sm:flex">
                Semua Kategori <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}"
                    class="group relative overflow-hidden rounded-2xl border border-neutral-100 bg-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="aspect-[4/3] overflow-hidden rounded-xl bg-neutral-100">
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" loading="lazy"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xl">{{ $category->icon }}</span>
                        <div>
                            <p class="text-sm font-bold text-neutral-900 group-hover:text-brand-600">{{ $category->name }}</p>
                            <p class="text-xs text-neutral-400">{{ $category->products_count }} produk</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="container-page pb-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-brand-500">Produk Terbaru</p>
                <h2 class="mt-1 text-2xl font-extrabold text-neutral-900 sm:text-3xl">Baru Rilis</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-neutral-700 hover:text-brand-600 sm:flex">
                Lihat Semua <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @foreach ($latestProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    <section class="container-page pb-16">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-600 via-brand-500 to-amber-400 px-8 py-12 text-white sm:px-14">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 30%, #fff 1px, transparent 1px), radial-gradient(circle at 70% 60%, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
                <div class="max-w-xl">
                    <p class="inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-bold uppercase tracking-wider">
                        <x-icon name="sparkles" class="h-4 w-4" /> Promo Spesial Bulan Ini
                    </p>
                    <h2 class="mt-4 text-3xl font-black leading-tight sm:text-4xl">Diskon Hingga 40% untuk Perlengkapan Favoritmu</h2>
                    <p class="mt-3 text-white/90">Gunakan voucher <span class="font-bold">GOSPORT10</span> dan dapatkan gratis ongkir untuk pembelian minimal Rp 300.000.</p>
                </div>
                <a href="{{ route('pages.promo') }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-full bg-neutral-900 px-7 py-3.5 text-sm font-bold text-white transition hover:bg-black">
                    Ambil Promo <x-icon name="arrow-right" class="h-5 w-5" />
                </a>
            </div>
        </div>
    </section>

    <section class="container-page pb-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-brand-500">Produk Terlaris</p>
                <h2 class="mt-1 text-2xl font-extrabold text-neutral-900 sm:text-3xl">Paling Banyak Dibeli</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'best']) }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-neutral-700 hover:text-brand-600 sm:flex">
                Lihat Semua <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @foreach ($bestSellers as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    <section class="container-page pb-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-brand-500">Promo & Diskon</p>
                <h2 class="mt-1 text-2xl font-extrabold text-neutral-900 sm:text-3xl">Harga Spesial Hari Ini</h2>
            </div>
            <a href="{{ route('pages.promo') }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-neutral-700 hover:text-brand-600 sm:flex">
                Lihat Semua <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @foreach ($promoProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    <section class="container-page pb-20">
        <div class="grid gap-6 rounded-3xl border border-neutral-100 bg-white p-8 shadow-sm lg:grid-cols-[1.2fr_1fr] lg:p-12">
            <div>
                <h2 class="text-2xl font-extrabold text-neutral-900 sm:text-3xl">Dapatkan Info Promo & Produk Baru</h2>
                <p class="mt-3 max-w-lg text-neutral-600">
                    Berlangganan newsletter GoSport dan jadilah yang pertama tahu tentang diskon, produk baru, dan tips olahraga.
                </p>
            </div>
            <form class="flex flex-col gap-3 sm:flex-row" onsubmit="event.preventDefault(); this.reset(); alert('Terima kasih telah berlangganan newsletter GoSport!');">
                <input type="email" required placeholder="Alamat email kamu"
                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-100">
                <button type="submit" class="shrink-0 rounded-xl bg-neutral-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-500">
                    Berlangganan
                </button>
            </form>
        </div>
    </section>
@endsection
