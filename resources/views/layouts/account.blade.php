@extends('layouts.app')

@section('content')
    <div class="border-b border-neutral-200 bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="font-medium text-neutral-900">@yield('account-title', 'Akun Saya')</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-neutral-900">@yield('account-title', 'Akun Saya')</h1>
        </div>
    </div>

    <div class="container-page grid gap-8 py-10 lg:grid-cols-[280px_1fr]">
        <aside class="lg:sticky lg:top-32 lg:self-start">
            <div class="rounded-3xl border border-neutral-100 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-3 rounded-2xl bg-neutral-900 p-4 text-white">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-brand-500 text-lg font-black text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-neutral-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                @php
                    $menu = [
                        ['account.dashboard', 'grid', 'Dashboard'],
                        ['account.profile', 'user', 'Profil Saya'],
                        ['account.addresses', 'map-pin', 'Alamat Saya'],
                        ['account.orders', 'clipboard', 'Riwayat Pesanan'],
                        ['account.wishlist', 'heart', 'Wishlist'],
                        ['cart.index', 'cart', 'Keranjang'],
                    ];
                @endphp

                <nav class="mt-3 space-y-1">
                    @foreach ($menu as [$route, $icon, $label])
                        <a href="{{ route($route) }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs($route) ? 'bg-brand-50 text-brand-600' : 'text-neutral-600 hover:bg-neutral-50' }}">
                            <x-icon :name="$icon" class="h-5 w-5 {{ request()->routeIs($route) ? 'text-brand-500' : 'text-neutral-400' }}" />
                            {{ $label }}
                        </a>
                    @endforeach
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-neutral-600 transition hover:bg-neutral-50">
                            <x-icon name="cog" class="h-5 w-5 text-neutral-400" /> Dashboard Admin
                        </a>
                    @endif
                </nav>

                <form action="{{ route('logout') }}" method="POST" class="mt-3 border-t border-neutral-100 pt-3">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50">
                        <x-icon name="logout" class="h-5 w-5" /> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-w-0">
            @yield('account-content')
        </div>
    </div>
@endsection
