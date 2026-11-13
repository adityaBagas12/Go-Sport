@php
    $navCategories = \App\Models\Category::withCount('products')->orderBy('name')->take(8)->get();
@endphp

<div class="border-b border-[#8CC4FF]/20 bg-[#071E43] text-white">
    <div class="container-page flex h-9 items-center justify-between gap-4 text-xs">
        <p class="flex items-center gap-2 truncate">
            <x-icon name="truck" class="h-4 w-4 text-[#3D9BFF]" />
            <span class="truncate">Gratis ongkir min. belanja Rp 300.000 &middot; Voucher <span class="font-bold text-[#FF7A00]">GOSPORT10</span> diskon 10%</span>
        </p>
        <div class="hidden shrink-0 items-center gap-4 sm:flex">
            <span class="flex items-center gap-1.5 text-neutral-300"><x-icon name="headset" class="h-4 w-4 text-[#0A6CFF]" /> 0800-1-GOSPORT</span>
        </div>
    </div>
</div>

<header class="sticky top-0 z-40 border-b border-[#8CC4FF]/30 bg-[#0A2A5E] text-white shadow-md backdrop-blur">
    <div class="container-page flex h-16 items-center gap-3">
        <button data-mobile-menu-toggle class="grid h-10 w-10 place-items-center rounded-xl text-white hover:bg-white/10 lg:hidden" aria-label="Buka menu">
            <x-icon name="menu" class="h-6 w-6" />
        </button>

        <x-logo />

        <form action="{{ route('products.index') }}" method="GET" class="relative ml-2 hidden flex-1 items-center md:flex">
            <x-icon name="search" class="pointer-events-none absolute left-4 h-5 w-5 text-neutral-400" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari sepatu, jersey, bola, raket..."
                class="w-full rounded-full border border-[#8CC4FF] bg-white py-2.5 pl-11 pr-4 text-sm text-[#10213F] outline-none transition focus:border-[#0A6CFF] focus:ring-2 focus:ring-[#8CC4FF]">
        </form>

        <div class="ml-auto flex items-center gap-1">
            <a href="{{ route('account.wishlist') }}" class="relative grid h-10 w-10 place-items-center rounded-xl text-[#E6F1FF] transition hover:bg-white/10 hover:text-white" aria-label="Wishlist">
                <x-icon name="heart" class="h-6 w-6 text-[#8CC4FF] hover:text-[#3D9BFF]" />
                @if ($globalWishlistCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 grid h-5 min-w-5 place-items-center rounded-full bg-[#FF7A00] px-1 text-[11px] font-black text-[#10213F]">{{ $globalWishlistCount }}</span>
                @endif
            </a>

            <a href="{{ route('cart.index') }}" class="relative grid h-10 w-10 place-items-center rounded-xl text-[#E6F1FF] transition hover:bg-white/10 hover:text-white" aria-label="Keranjang">
                <x-icon name="cart" class="h-6 w-6 text-[#8CC4FF] hover:text-[#3D9BFF]" />
                @if ($globalCartCount > 0)
                    <span class="absolute -right-0.5 -top-0.5 grid h-5 min-w-5 place-items-center rounded-full bg-[#FF7A00] px-1 text-[11px] font-black text-[#10213F]">{{ $globalCartCount }}</span>
                @endif
            </a>

            <div class="relative">
                <button data-dropdown-toggle="account-menu" class="flex items-center gap-2 rounded-xl px-2 py-2 text-[#E6F1FF] transition hover:bg-white/10 hover:text-white">
                    <x-icon name="user" class="h-6 w-6 text-[#8CC4FF]" />
                    <span class="hidden text-sm font-semibold lg:block">
                        {{ auth()->check() ? Str::limit(auth()->user()->name, 12) : 'Akun' }}
                    </span>
                    <x-icon name="chevron-down" class="hidden h-4 w-4 lg:block" />
                </button>

                <div id="account-menu" data-dropdown-menu class="absolute right-0 mt-2 hidden w-56 overflow-hidden rounded-2xl border border-[#8CC4FF] bg-white py-2 text-[#10213F] shadow-xl">
                    @auth
                        <div class="border-b border-[#E6F1FF] px-4 pb-3 pt-1">
                            <p class="truncate text-sm font-bold text-[#0A2A5E]">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-neutral-500">{{ auth()->user()->email }}</p>
                        </div>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-[#10213F] hover:bg-[#E6F1FF]">
                                <x-icon name="grid" class="h-5 w-5 text-[#0A6CFF]" /> Dashboard Admin
                            </a>
                        @endif
                        <a href="{{ route('account.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#10213F] hover:bg-[#E6F1FF]">
                            <x-icon name="user" class="h-5 w-5 text-[#0A6CFF]" /> Akun Saya
                        </a>
                        <a href="{{ route('account.orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#10213F] hover:bg-[#E6F1FF]">
                            <x-icon name="clipboard" class="h-5 w-5 text-[#0A6CFF]" /> Riwayat Pesanan
                        </a>
                        <a href="{{ route('account.wishlist') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#10213F] hover:bg-[#E6F1FF]">
                            <x-icon name="heart" class="h-5 w-5 text-[#0A6CFF]" /> Wishlist
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="border-t border-[#E6F1FF]">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-[#E53935] hover:bg-red-50">
                                <x-icon name="logout" class="h-5 w-5" /> Keluar
                            </button>
                        </form>
                    @else
                        <div class="space-y-2 px-4 py-2">
                            <a href="{{ route('login') }}" class="block rounded-xl bg-[#0A6CFF] py-2.5 text-center text-sm font-bold text-white transition hover:bg-[#3D9BFF]">Masuk</a>
                            <a href="{{ route('register') }}" class="block rounded-xl border border-[#8CC4FF] py-2.5 text-center text-sm font-semibold text-[#0A2A5E] transition hover:bg-[#E6F1FF]">Daftar</a>
                        </div>
                        <div class="mt-1 border-t border-[#E6F1FF] pt-1">
                            <a href="{{ route('account.orders') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#10213F] hover:bg-[#E6F1FF]">
                                <x-icon name="clipboard" class="h-5 w-5 text-[#0A6CFF]" /> Lacak Pesanan
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <nav class="hidden border-t border-[#8CC4FF]/20 lg:block">
        <div class="container-page flex h-12 items-center gap-1 text-sm font-medium">
            <a href="{{ route('home') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('home') ? 'bg-[#0A6CFF] text-white font-bold' : 'text-[#E6F1FF] hover:bg-white/10 hover:text-white' }}">Home</a>
            <a href="{{ route('products.index') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('products.*') ? 'bg-[#0A6CFF] text-white font-bold' : 'text-[#E6F1FF] hover:bg-white/10 hover:text-white' }}">Produk</a>

            <div class="relative">
                <button data-dropdown-toggle="category-menu" class="flex items-center gap-1 rounded-lg px-4 py-2 text-[#E6F1FF] transition hover:bg-white/10 hover:text-white">
                    Kategori <x-icon name="chevron-down" class="h-4 w-4" />
                </button>
                <div id="category-menu" data-dropdown-menu class="absolute left-0 top-full z-50 hidden w-64 overflow-hidden rounded-2xl border border-[#8CC4FF] bg-white py-2 text-[#10213F] shadow-xl">
                    @foreach ($navCategories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" class="flex items-center justify-between px-4 py-2.5 text-sm text-[#10213F] hover:bg-[#E6F1FF]">
                            <span class="flex items-center gap-3">
                                <span class="text-base">{{ $category->icon }}</span> {{ $category->name }}
                            </span>
                            <span class="text-xs text-neutral-400">{{ $category->products_count }}</span>
                        </a>
                    @endforeach
                    <a href="{{ route('categories.index') }}" class="mt-1 flex items-center gap-2 border-t border-[#E6F1FF] px-4 py-2.5 text-sm font-bold text-[#0A6CFF] hover:bg-[#E6F1FF]">
                        Lihat semua kategori <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            </div>

            <a href="{{ route('pages.promo') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('pages.promo') ? 'bg-[#0A6CFF] text-white font-bold' : 'text-[#E6F1FF] hover:bg-white/10 hover:text-white' }}">Promo</a>
            <a href="{{ route('pages.about') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('pages.about') ? 'bg-[#0A6CFF] text-white font-bold' : 'text-[#E6F1FF] hover:bg-white/10 hover:text-white' }}">Tentang Kami</a>
            <a href="{{ route('pages.contact') }}" class="rounded-lg px-4 py-2 transition {{ request()->routeIs('pages.contact') ? 'bg-[#0A6CFF] text-white font-bold' : 'text-[#E6F1FF] hover:bg-white/10 hover:text-white' }}">Kontak</a>
        </div>
    </nav>

    <div id="mobile-menu" class="hidden border-t border-[#8CC4FF] bg-white text-[#10213F] lg:hidden">
        <div class="container-page space-y-1 py-4">
            <form action="{{ route('products.index') }}" method="GET" class="relative mb-3 flex items-center md:hidden">
                <x-icon name="search" class="pointer-events-none absolute left-4 h-5 w-5 text-neutral-400" />
                <input type="text" name="search" placeholder="Cari produk..."
                    class="w-full rounded-full border border-[#8CC4FF] bg-white py-2.5 pl-11 pr-4 text-sm text-[#10213F] outline-none focus:border-[#0A6CFF]">
            </form>
            <a href="{{ route('home') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#0A2A5E] hover:bg-[#E6F1FF]">Home</a>
            <a href="{{ route('products.index') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#0A2A5E] hover:bg-[#E6F1FF]">Produk</a>
            <a href="{{ route('categories.index') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#0A2A5E] hover:bg-[#E6F1FF]">Kategori</a>
            <a href="{{ route('pages.promo') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#0A2A5E] hover:bg-[#E6F1FF]">Promo</a>
            <a href="{{ route('pages.about') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#0A2A5E] hover:bg-[#E6F1FF]">Tentang Kami</a>
            <a href="{{ route('pages.contact') }}" class="block rounded-xl px-4 py-3 font-semibold text-[#0A2A5E] hover:bg-[#E6F1FF]">Kontak</a>
        </div>
    </div>
</header>
