@extends('layouts.app')

@section('content')
    <div class="border-b border-[#8CC4FF]/30 bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-[#0A6CFF]">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="font-medium text-[#10213F]">@yield('account-title', 'Akun Saya')</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-[#0A2A5E]">@yield('account-title', 'Akun Saya')</h1>
        </div>
    </div>

    <div class="container-page grid gap-8 py-10 lg:grid-cols-[280px_1fr]">
        <aside class="lg:sticky lg:top-32 lg:self-start">
            <div class="rounded-3xl border border-[#8CC4FF] bg-white p-4 shadow-sm">
                <div class="flex items-center gap-3 rounded-2xl bg-[#0A2A5E] p-4 text-white">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-[#0A6CFF] text-lg font-black text-white shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-blue-200/80">{{ auth()->user()->email }}</p>
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
                            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs($route) ? 'border border-[#8CC4FF]/60 bg-[#E6F1FF] text-[#0A6CFF]' : 'text-[#10213F] hover:bg-[#F4F8FF]' }}">
                            <x-icon :name="$icon" class="h-5 w-5 {{ request()->routeIs($route) ? 'text-[#0A6CFF]' : 'text-neutral-400' }}" />
                            {{ $label }}
                        </a>
                    @endforeach
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-[#0A2A5E] transition hover:bg-[#E6F1FF]">
                            <x-icon name="cog" class="h-5 w-5 text-[#0A6CFF]" /> Dashboard Admin
                        </a>
                    @endif
                </nav>

                <form action="{{ route('logout') }}" method="POST" class="mt-3 border-t border-[#8CC4FF]/30 pt-3">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-[#E53935] transition hover:bg-[#E53935]/10">
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
