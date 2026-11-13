@extends('layouts.app')

@section('title', 'GoSport - Lengkapi Perlengkapan Olahragamu')

@section('content')
    {{-- Hero Section (Sekunder: #0A2A5E) --}}
    <section class="relative overflow-hidden bg-[#0A2A5E] text-white">
        <div class="absolute inset-0 opacity-15" style="background-image: linear-gradient(115deg, transparent 0 45%, #0A6CFF 45% 47%, transparent 47%), linear-gradient(115deg, transparent 0 60%, #FF7A00 60% 62%, transparent 62%);"></div>
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-[#0A6CFF]/25 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-[#FF7A00]/20 blur-3xl"></div>

        <div class="container-page relative grid items-center gap-12 py-16 lg:grid-cols-2 lg:py-24">
            <div class="animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full border border-[#8CC4FF]/40 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-[#8CC4FF]">
                    <x-icon name="bolt" class="h-4 w-4 text-[#FF7A00]" /> Toko Olahraga #1 di Indonesia
                </span>
                <h1 class="mt-5 text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Lengkapi Perlengkapan <span class="text-[#FF7A00]">Olahragamu</span> Bersama GoSport
                </h1>
                <p class="mt-5 max-w-lg text-lg text-[#E6F1FF]/90">
                    Temukan produk olahraga berkualitas original dengan harga terbaik untuk pria, wanita, dan anak.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    {{-- Aksen CTA: Tombol Beli / Checkout (#FF7A00 dengan teks #10213F) --}}
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-[#FF7A00] px-7 py-3.5 text-sm font-extrabold text-[#10213F] shadow-lg shadow-[#FF7A00]/30 transition hover:-translate-y-0.5 hover:bg-[#ff8f26]">
                        Belanja Sekarang <x-icon name="arrow-right" class="h-5 w-5" />
                    </a>
                    {{-- Tombol sekunder: Primer (#0A6CFF) / border (#8CC4FF) --}}
                    <a href="{{ route('categories.index') }}"
                        class="inline-flex items-center gap-2 rounded-full border-2 border-[#8CC4FF] px-7 py-3.5 text-sm font-bold text-white transition hover:bg-[#0A6CFF] hover:border-[#0A6CFF]">
                        Lihat Kategori
                    </a>
                </div>

                <dl class="mt-10 grid max-w-md grid-cols-3 gap-6">
                    <div>
                        <dt class="text-2xl font-black text-[#FF7A00]">1.500+</dt>
                        <dd class="text-xs text-[#8CC4FF]">Produk Original</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-black text-[#FF7A00]">50rb+</dt>
                        <dd class="text-xs text-[#8CC4FF]">Pelanggan Puas</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-black text-[#FF7A00]">4.9/5</dt>
                        <dd class="text-xs text-[#8CC4FF]">Rating Toko</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="border-t border-[#8CC4FF]/25 bg-[#071E43]">
            <div class="container-page grid grid-cols-2 gap-6 py-6 lg:grid-cols-4">
                @foreach ([
                    ['truck', 'Pengiriman Cepat', 'Ke seluruh Indonesia'],
                    ['shield', '100% Original', 'Produk bergaransi'],
                    ['refresh', 'Retur 7 Hari', 'Tanpa ribet'],
                    ['headset', 'CS 24/7', 'Siap membantu'],
                ] as [$icon, $title, $subtitle])
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-[#0A6CFF]/20 text-[#3D9BFF]">
                            <x-icon :name="$icon" class="h-6 w-6" />
                        </span>
                        <div>
                            <p class="text-sm font-bold text-white">{{ $title }}</p>
                            <p class="text-xs text-[#8CC4FF]">{{ $subtitle }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Kategori (Permukaan selang-seling: #F4F8FF) --}}
    <section class="container-page py-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-[#0A6CFF]">Kategori Olahraga</p>
                <h2 class="mt-1 text-2xl font-extrabold text-[#0A2A5E] sm:text-3xl">Pilih Sesuai Cabangmu</h2>
            </div>
            <a href="{{ route('categories.index') }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-bold text-[#0A6CFF] hover:text-[#3D9BFF] sm:flex">
                Semua Kategori <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}"
                    class="group relative overflow-hidden rounded-2xl border border-[#8CC4FF] bg-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#0A6CFF] hover:shadow-xl">
                    <div class="aspect-[4/3] overflow-hidden rounded-xl bg-[#E6F1FF]">
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" loading="lazy"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xl">{{ $category->icon }}</span>
                        <div>
                            <p class="text-sm font-bold text-[#0A2A5E] group-hover:text-[#0A6CFF]">{{ $category->name }}</p>
                            <p class="text-xs text-neutral-500">{{ $category->products_count }} produk</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Section Selang-Seling (Permukaan #E6F1FF) --}}
    <section class="border-y border-[#8CC4FF]/40 bg-[#E6F1FF] py-16">
        <div class="container-page">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wider text-[#0A6CFF]">Produk Terbaru</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-[#0A2A5E] sm:text-3xl">Baru Rilis</h2>
                </div>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-bold text-[#0A6CFF] hover:text-[#3D9BFF] sm:flex">
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

    {{-- Banner Promo (Sekunder #0A2A5E ke Primer #0A6CFF dengan Tombol Aksen #FF7A00) --}}
    <section class="container-page py-16">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0A2A5E] via-[#0A6CFF] to-[#3D9BFF] px-8 py-12 text-white shadow-xl sm:px-14">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 30%, #fff 1px, transparent 1px), radial-gradient(circle at 70% 60%, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
                <div class="max-w-xl">
                    <p class="inline-flex items-center gap-2 rounded-full bg-[#FF7A00] px-3.5 py-1 text-xs font-black uppercase tracking-wider text-[#10213F]">
                        <x-icon name="sparkles" class="h-4 w-4 text-[#10213F]" /> Promo Spesial Bulan Ini
                    </p>
                    <h2 class="mt-4 text-3xl font-black leading-tight text-white sm:text-4xl">Diskon Hingga 40% untuk Perlengkapan Favoritmu</h2>
                    <p class="mt-3 text-[#E6F1FF]">Gunakan voucher <span class="font-extrabold text-[#FF7A00]">GOSPORT10</span> dan dapatkan gratis ongkir untuk pembelian minimal Rp 300.000.</p>
                </div>
                <a href="{{ route('pages.promo') }}"
                    class="inline-flex shrink-0 items-center gap-2 rounded-full bg-[#FF7A00] px-7 py-3.5 text-sm font-black text-[#10213F] shadow-lg shadow-[#FF7A00]/30 transition hover:-translate-y-0.5 hover:bg-[#ff8f26]">
                    Ambil Promo <x-icon name="arrow-right" class="h-5 w-5 text-[#10213F]" />
                </a>
            </div>
        </div>
    </section>

    {{-- Produk Terlaris (Permukaan #FFFFFF) --}}
    <section class="container-page pb-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-[#0A6CFF]">Produk Terlaris</p>
                <h2 class="mt-1 text-2xl font-extrabold text-[#0A2A5E] sm:text-3xl">Paling Banyak Dibeli</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'best']) }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-bold text-[#0A6CFF] hover:text-[#3D9BFF] sm:flex">
                Lihat Semua <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @foreach ($bestSellers as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>

    {{-- Promo & Diskon (Section Selang-Seling #E6F1FF) --}}
    <section class="border-y border-[#8CC4FF]/40 bg-[#E6F1FF] py-16">
        <div class="container-page">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wider text-[#0A6CFF]">Promo & Diskon</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-[#0A2A5E] sm:text-3xl">Harga Spesial Hari Ini</h2>
                </div>
                <a href="{{ route('pages.promo') }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-bold text-[#0A6CFF] hover:text-[#3D9BFF] sm:flex">
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

    {{-- Newsletter (Permukaan Kartu #FFFFFF dengan Border #8CC4FF) --}}
    <section class="container-page py-20">
        <div class="grid gap-6 rounded-3xl border-2 border-[#8CC4FF] bg-white p-8 shadow-sm lg:grid-cols-[1.2fr_1fr] lg:p-12">
            <div>
                <h2 class="text-2xl font-extrabold text-[#0A2A5E] sm:text-3xl">Dapatkan Info Promo & Produk Baru</h2>
                <p class="mt-3 max-w-lg text-[#10213F]/80">
                    Berlangganan newsletter GoSport dan jadilah yang pertama tahu tentang diskon, produk baru, dan tips olahraga.
                </p>
            </div>
            <form class="flex flex-col gap-3 sm:flex-row" onsubmit="event.preventDefault(); this.reset(); alert('Terima kasih telah berlangganan newsletter GoSport!');">
                <input type="email" required placeholder="Alamat email kamu"
                    class="w-full rounded-xl border border-[#8CC4FF] bg-[#F4F8FF] px-4 py-3 text-sm text-[#10213F] outline-none focus:border-[#0A6CFF] focus:bg-white focus:ring-2 focus:ring-[#8CC4FF]">
                <button type="submit" class="shrink-0 rounded-xl bg-[#0A6CFF] px-7 py-3 text-sm font-bold text-white transition hover:bg-[#3D9BFF]">
                    Berlangganan
                </button>
            </form>
        </div>
    </section>
@endsection
