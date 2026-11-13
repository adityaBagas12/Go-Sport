@php
    $navCategories = \App\Models\Category::withCount('products')->orderBy('name')->take(8)->get();
@endphp

<div class="bg-neutral-900 text-white">
    <div class="container-page flex h-9 items-center justify-between gap-4 text-xs">
        <p class="flex items-center gap-2 truncate">
            <x-icon name="truck" class="h-4 w-4 text-brand-500" />
            <span class="truncate">Gratis ongkir min. belanja Rp 300.000 &middot; Voucher <span class="font-semibold text-brand-400">GOSPORT10</span> diskon 10%</span>
        </p>
        <div class="hidden shrink-0 items-center gap-4 sm:flex">
            <span class="flex items-center gap-1.5"><x-icon name="headset" class="h-4 w-4" /> 0800-1-GOSPORT</span>
        </div>
    </div>
</div>

<header class="sticky top-0 z-40 border-b border-neutral-200 bg-white/95 backdrop-blur">
    <div class="container-page flex h-16 items-center gap-3">
        <button data-mobile-menu-toggle class="grid h-10 w-10 place-items-center rounded-xl text-neutral-700 hover:bg-neutral-100 lg:hidden" aria-label="Buka menu">
            <x-icon name="menu" class="h-6 w-6" />
        </button>

        <x-logo />

        <form action="{{ route('products.index') }}" method="GET" class="relative ml-2 hidden flex-1 items-center md:flex">
            <x-icon name="search" class="pointer-events-none absolute left-4 h-5 w-5 text-neutral-400" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari sepatu, jersey, bola, raket..."
                class="w-full rounded-full border border-neutral-200 bg-neutral-100 py-2.5 pl-11 pr-4 text-sm outline-none transition focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-100">
        </form>

        <div class="ml-auto flex items-center gap-1">
            <a href="{{ route('account.wishlist') }}" class="relative grid h-10 w-10 place-items-center rounded-xl text-neutral-700 transition hover:bg-neutral-100" aria-label="Wishlist">
                <x-icon name="heart" class="h-6 w-6" />
                @if ($globalWishlistCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 grid h-5 min-w-5 place-items-center rounded-full bg-brand-500 px-1 text-[11px] font-bold text-white">{{ $globalWishlistCount }}</span>
                @endif
            </a>

            <a href="{{ route('cart.index') }}" class="relative grid h-10 w-10 place-items-center rounded-xl text-neutral-700 transition hover:bg-neutral-100" aria-label="Keranjang">
                <x-icon name="cart" class="h-6 w-6" />
                @if ($globalCartCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 grid h-5 min-w-5 place-items-center rounded-full bg-brand-500 px-1 text-[11px] font-bold text-white">{{ $globalCartCount }}</span>
                @endif
            </a>

            <div class="relative">
                <button data-dropdown-toggle="account-menu" class="flex items-center gap-2 rounded-xl px-2 py-2 text-neutral-700 transition hover:bg-neutral-100">
                    <x-icon name="user" class="h-6 w-6" />
                    <span class="hidden text-sm font-semibold lg:block">
                        {{ auth()->check() ? Str::limit(auth()->user()->name, 12) : 'Akun' }}
                    </span>
                    <x-icon name="chevron-down" class="hidden h-4 w-4 lg:block" />
                </button>

                <div id="account-menu" data-dropdown-menu class="absolute right-0 mt-2 hidden w-56 overflow-hidden rounded-2xl border border-neutral-100 bg-white py-2 shadow-xl">
                    @auth
                        <div class="border-b border-neutral-100 px-4 pb-3 pt-1">
                            <p class="truncate text-sm font-semibold text-neutral-900">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-neutral-500">{{ auth()->user()->email }}</p>
                        </div>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
                                <x-icon name="grid" class="h-5 w-5 text-brand-500" /> Dashboard Admin
                            </a>
                        @endif
                        <a href="{{ route('account.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-neutral-700 hover:bg-neutral-50">
                            <x-icon name="user" class="h-5 w-5 text-neutral-400" /> Akun Saya
                        </a>
                        <a href="{{ route('account.orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-neutral-700 hover:bg-neutral-50">
                            <x-icon name="clipboard" class="h-5 w-5 text-neutral-400" /> Riwayat Pesanan
                        </a>
                        <a href="{{ route('account.wishlist') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-neutral-700 hover:bg-neutral-50">
                            <x-icon name="heart" class="h-5 w-5 text-neutral-400" /> Wishlist
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="border-t border-neutral-100">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                <x-icon name="logout" class="h-5 w-5" /> Keluar
                            </button>
                        </form>
                    @else
                        <div class="space-y-2 px-4 py-2">
                            <a href="{{ route('login') }}" class="block rounded-xl bg-neutral-900 py-2.5 text-center text-sm font-semibold text-white hover:bg-neutral-800">Masuk</a>
                            <a href="{{ route('register') }}" class="block rounded-xl border border-neutral-200 py-2.5 text-center text-sm font-semibold text-neutral-700 hover:bg-neutral-50">Daftar</a>
                        </div>
                        <div class="mt-1 border-t border-neutral-100 pt-1">
                            <a href="{{ route('account.orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-neutral-700 hover:bg-neutral-50">
                                <x-icon name="clipboard" class="h-5 w-5 text-neutral-400" /> Lacak Pesanan
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <nav class="hidden border-t border-neutral-100 lg:block">
        <div class="container-page flex h-12 items-center gap-1 text-sm font-medium">
            <a href="{{ route('home') }}" class="rounded-lg px-4 py-2 {{ request()->routeIs('home') ? 'bg-neutral-900 text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">Home</a>
            <a href="{{ route('products.index') }}" class="rounded-lg px-4 py-2 {{ request()->routeIs('products.*') ? 'bg-neutral-900 text-white' : 'text-neutral-600 hover:bg-neutral-100' }}">Produk</a>

            <div class="relative">
                <button data-dropdown-toggle="category-menu" class="flex items-center gap-1 rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100">
                    Kategori <x-icon name="chevron-down" class="h-4 w-4" />
                </button>
                <div id="category-menu" data-dropdown-menu class="absolute left-0 top-full z-50 hidden w-64 overflow-hidden rounded-2xl border border-neutral-100 bg-white py-2 shadow-xl">
                    @foreach ($navCategories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" class="flex items-center justify-between px-4 py-2.5 text-sm text-neutral-700 hover:bg-neutral-50">
                            <span class="flex items-center gap-3">
                                <span class="text-base">{{ $category->icon }}</span> {{ $category->name }}
                            </span>
                            <span class="text-xs text-neutral-400">{{ $category->products_count }}</span>
                        </a>
                    @endforeach
                    <a href="{{ route('categories.index') }}" class="mt-1 flex items-center gap-2 border-t border-neutral-100 px-4 py-2.5 text-sm font-semibold text-brand-600 hover:bg-brand-50">
                        Lihat semua kategori <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            </div>

            <a href="{{ route('pages.promo') }}" class="rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100">Promo</a>
            <a href="{{ route('pages.about') }}" class="rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100">Tentang Kami</a>
            <a href="{{ route('pages.contact') }}" class="rounded-lg px-4 py-2 text-neutral-600 hover:bg-neutral-100">Kontak</a>
        </div>
    </nav>

    <div id="mobile-menu" class="hidden border-t border-neutral-100 bg-white lg:hidden">
        <div class="container-page space-y-1 py-4">
            <form action="{{ route('products.index') }}" method="GET" class="relative mb-3 flex items-center md:hidden">
                <x-icon name="search" class="pointer-events-none absolute left-4 h-5 w-5 text-neutral-400" />
                <input type="text" name="search" placeholder="Cari produk..."
                    class="w-full rounded-full border border-neutral-200 bg-neutral-100 py-2.5 pl-11 pr-4 text-sm outline-none focus:border-brand-500">
            </form>
            <a href="{{ route('home') }}" class="block rounded-xl px-4 py-3 font-medium text-neutral-700 hover:bg-neutral-100">Home</a>
            <a href="{{ route('products.index') }}" class="block rounded-xl px-4 py-3 font-medium text-neutral-700 hover:bg-neutral-100">Produk</a>
            <a href="{{ route('categories.index') }}" class="block rounded-xl px-4 py-3 font-medium text-neutral-700 hover:bg-neutral-100">Kategori</a>
            <a href="{{ route('pages.promo') }}" class="block rounded-xl px-4 py-3 font-medium text-neutral-700 hover:bg-neutral-100">Promo</a>
            <a href="{{ route('pages.about') }}" class="block rounded-xl px-4 py-3 font-medium text-neutral-700 hover:bg-neutral-100">Tentang Kami</a>
            <a href="{{ route('pages.contact') }}" class="block rounded-xl px-4 py-3 font-medium text-neutral-700 hover:bg-neutral-100">Kontak</a>
        </div>
    </div>
</header>
