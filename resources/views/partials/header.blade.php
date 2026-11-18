@php
    $navCategories = \App\Models\Category::withCount('products')->orderBy('name')->take(8)->get();
@endphp

<div class="border-b border-[#243F31] bg-[#2F4F3E] text-white">
    <div class="container-page flex h-9 items-center justify-between gap-4 text-xs">
        <p class="flex items-center gap-2 truncate">
            <x-icon name="truck" class="h-4 w-4 text-[#DCE8DF]" />
            <span class="truncate text-emerald-100/90">Gratis ongkir min. belanja Rp 300.000 &middot; Voucher <span class="font-bold text-[#DCE8DF]">GOSPORT10</span> diskon 10%</span>
        </p>
        <div class="hidden shrink-0 items-center gap-4 sm:flex">
            <span class="flex items-center gap-1.5 text-emerald-100/90"><x-icon name="headset" class="h-4 w-4 text-[#DCE8DF]" /> 0800-1-GOSPORT</span>
        </div>
    </div>
</div>

<header class="sticky top-0 z-40 border-b border-[#D5E0D8] bg-white text-[#1E2B24] shadow-sm backdrop-blur">
    <div class="container-page flex h-16 items-center gap-3">
        <button data-mobile-menu-toggle class="grid h-10 w-10 place-items-center rounded-xl text-[#1E2B24] hover:bg-[#F6F9F7] lg:hidden" aria-label="Buka menu">
            <x-icon name="menu" class="h-6 w-6" />
        </button>

        <x-logo />

        <form action="{{ route('products.index') }}" method="GET" class="relative ml-2 hidden flex-1 items-center md:flex">
            <x-icon name="search" class="pointer-events-none absolute left-4 h-5 w-5 text-[#5A6B61]" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari sepatu, jersey, bola, raket..."
                class="w-full rounded-full border border-[#D5E0D8] bg-[#F6F9F7] py-2.5 pl-11 pr-4 text-sm text-[#1E2B24] outline-none transition focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
        </form>

        <div class="ml-auto flex items-center gap-1">
            <a href="{{ route('account.wishlist') }}" class="relative grid h-10 w-10 place-items-center rounded-xl text-[#1E2B24] transition hover:bg-[#F6F9F7] hover:text-[#4F7A63]" aria-label="Wishlist">
                <x-icon name="heart" class="h-6 w-6" />
                @if ($globalWishlistCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 grid h-5 min-w-5 place-items-center rounded-full bg-[#4F7A63] px-1 text-[11px] font-black text-white">{{ $globalWishlistCount }}</span>
                @endif
            </a>

            <a href="{{ route('cart.index') }}" class="relative grid h-10 w-10 place-items-center rounded-xl text-[#1E2B24] transition hover:bg-[#F6F9F7] hover:text-[#4F7A63]" aria-label="Keranjang">
                <x-icon name="cart" class="h-6 w-6" />
                @if ($globalCartCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 grid h-5 min-w-5 place-items-center rounded-full bg-[#4F7A63] px-1 text-[11px] font-black text-white">{{ $globalCartCount }}</span>
                @endif
            </a>

            <div class="relative">
                <button data-dropdown-toggle="account-menu" class="flex items-center gap-2 rounded-xl px-2 py-2 text-[#1E2B24] transition hover:bg-[#F6F9F7] hover:text-[#4F7A63]">
                    <x-icon name="user" class="h-6 w-6" />
                    <span class="hidden text-sm font-semibold lg:block">
                        {{ auth()->check() ? Str::limit(auth()->user()->name, 12) : 'Akun' }}
                    </span>
                    <x-icon name="chevron-down" class="hidden h-4 w-4 lg:block" />
                </button>

                <div id="account-menu" data-dropdown-menu class="absolute right-0 mt-2 hidden w-56 overflow-hidden rounded-2xl border border-[#D5E0D8] bg-white py-2 text-[#1E2B24] shadow-xl">
                    @auth
                        <div class="border-b border-[#D5E0D8] px-4 pb-3 pt-1">
                            <p class="truncate text-sm font-bold text-[#1E2B2B]">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-[#5A6B61]">{{ auth()->user()->email }}</p>
                        </div>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-[#1E2B24] hover:bg-[#F6F9F7]">
                                <x-icon name="grid" class="h-5 w-5 text-[#4F7A63]" /> Dashboard Admin
                            </a>
                        @endif
                        <a href="{{ route('account.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#1E2B24] hover:bg-[#F6F9F7]">
                            <x-icon name="user" class="h-5 w-5 text-[#4F7A63]" /> Akun Saya
                        </a>
                        <a href="{{ route('account.orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#1E2B24] hover:bg-[#F6F9F7]">
                            <x-icon name="clipboard" class="h-5 w-5 text-[#4F7A63]" /> Riwayat Pesanan
                        </a>
                        <a href="{{ route('account.wishlist') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#1E2B24] hover:bg-[#F6F9F7]">
                            <x-icon name="heart" class="h-5 w-5 text-[#4F7A63]" /> Wishlist
                        </a>
                        <a href="{{ route('account.messages') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#1E2B24] hover:bg-[#F6F9F7]">
                            <x-icon name="mail" class="h-5 w-5 text-[#4F7A63]" /> Pesan & Bantuan
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="border-t border-[#D5E0D8]">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-[#E53935] hover:bg-red-50">
                                <x-icon name="logout" class="h-5 w-5" /> Keluar
                            </button>
                        </form>
                    @else
                        <div class="space-y-2 px-4 py-2">
                            <a href="{{ route('login') }}" class="block rounded-xl bg-[#4F7A63] py-2.5 text-center text-sm font-bold text-white transition hover:bg-[#3E6350]">Masuk</a>
                            <a href="{{ route('register') }}" class="block rounded-xl border border-[#D5E0D8] py-2.5 text-center text-sm font-semibold text-[#1E2B24] transition hover:bg-[#F6F9F7]">Daftar</a>
                        </div>
                        <div class="mt-1 border-t border-[#D5E0D8] pt-1">
                            <a href="{{ route('account.orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#1E2B24] hover:bg-[#F6F9F7]">
                                <x-icon name="clipboard" class="h-5 w-5 text-[#4F7A63]" /> Lacak Pesanan
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <nav class="hidden border-t border-[#D5E0D8] lg:block">
        <div class="container-page flex h-12 items-center gap-1 text-sm font-medium">
            <a href="{{ route('home') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('home') ? 'bg-[#4F7A63] text-white font-bold' : 'text-[#1E2B24] hover:bg-[#F6F9F7] hover:text-[#4F7A63]' }}">Home</a>
            <a href="{{ route('products.index') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('products.*') ? 'bg-[#4F7A63] text-white font-bold' : 'text-[#1E2B24] hover:bg-[#F6F9F7] hover:text-[#4F7A63]' }}">Produk</a>

            <div class="relative">
                <button data-dropdown-toggle="category-menu" class="flex items-center gap-1 rounded-lg px-4 py-2 text-[#1E2B24] transition hover:bg-[#F6F9F7] hover:text-[#4F7A63]">
                    Kategori <x-icon name="chevron-down" class="h-4 w-4" />
                </button>
                <div id="category-menu" data-dropdown-menu class="absolute left-0 top-full z-50 hidden w-64 overflow-hidden rounded-2xl border border-[#D5E0D8] bg-white py-2 text-[#1E2B24] shadow-xl">
                    @foreach ($navCategories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" class="flex items-center justify-between px-4 py-2.5 text-sm text-[#1E2B24] hover:bg-[#F6F9F7]">
                            <span class="flex items-center gap-3">
                                <span class="text-base">{{ $category->icon }}</span> {{ $category->name }}
                            </span>
                            <span class="text-xs text-[#5A6B61]">{{ $category->products_count }}</span>
                        </a>
                    @endforeach
                    <a href="{{ route('categories.index') }}" class="mt-1 flex items-center gap-2 border-t border-[#D5E0D8] px-4 py-2.5 text-sm font-bold text-[#4F7A63] hover:bg-[#F6F9F7]">
                        Lihat semua kategori <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            </div>

            <a href="{{ route('pages.promo') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('pages.promo') ? 'bg-[#4F7A63] text-white font-bold' : 'text-[#1E2B24] hover:bg-[#F6F9F7] hover:text-[#4F7A63]' }}">Promo</a>
            <a href="{{ route('pages.about') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('pages.about') ? 'bg-[#4F7A63] text-white font-bold' : 'text-[#1E2B24] hover:bg-[#F6F9F7] hover:text-[#4F7A63]' }}">Tentang Kami</a>
            <a href="{{ route('pages.contact') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('pages.contact') ? 'bg-[#4F7A63] text-white font-bold' : 'text-[#1E2B24] hover:bg-[#F6F9F7] hover:text-[#4F7A63]' }}">Kontak</a>
        </div>
    </nav>

    <div id="mobile-menu" class="hidden border-t border-[#D5E0D8] bg-white text-[#1E2B24] lg:hidden">
        <div class="container-page space-y-1 py-4">
            <form action="{{ route('products.index') }}" method="GET" class="relative mb-3 flex items-center md:hidden">
                <x-icon name="search" class="pointer-events-none absolute left-4 h-5 w-5 text-[#5A6B61]" />
                <input type="text" name="search" placeholder="Cari produk..."
                    class="w-full rounded-full border border-[#D5E0D8] bg-[#F6F9F7] py-2.5 pl-11 pr-4 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63]">
            </form>
            <a href="{{ route('home') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#1E2B24] hover:bg-[#F6F9F7]">Home</a>
            <a href="{{ route('products.index') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#1E2B24] hover:bg-[#F6F9F7]">Produk</a>
            <a href="{{ route('categories.index') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#1E2B24] hover:bg-[#F6F9F7]">Kategori</a>
            <a href="{{ route('pages.promo') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#1E2B24] hover:bg-[#F6F9F7]">Promo</a>
            <a href="{{ route('pages.about') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#1E2B24] hover:bg-[#F6F9F7]">Tentang Kami</a>
            <a href="{{ route('pages.contact') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#1E2B24] hover:bg-[#F6F9F7]">Kontak</a>
        </div>
    </div>
</header>
