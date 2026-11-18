@extends('layouts.app')

@section('content')
    <div class="border-b border-[#D5E0D8] bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-[#5A6B61]">
                <a href="{{ route('home') }}" class="hover:text-[#4F7A63]">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="font-medium text-[#1E2B24]">@yield('account-title', 'Akun Saya')</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-[#1E2B24]">@yield('account-title', 'Akun Saya')</h1>
        </div>
    </div>

    <div class="container-page grid gap-8 py-10 lg:grid-cols-[280px_1fr]">
        <aside class="lg:sticky lg:top-32 lg:self-start">
            <div class="rounded-3xl border border-[#D5E0D8] bg-white p-4 shadow-sm">
                <div class="flex items-center gap-3 rounded-2xl bg-[#2F4F3E] p-4 text-white">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-[#4F7A63] text-lg font-black text-white shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-emerald-200/70">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                @php
                    $unreadReplies = \App\Models\ContactMessage::where(function ($query) {
                        $query->where('user_id', auth()->id())
                            ->orWhere('email', auth()->user()->email);
                    })->whereNotNull('admin_reply')->whereNull('customer_read_at')->count();

                    $menu = [
                        ['account.dashboard', 'grid', 'Dashboard', null],
                        ['account.profile', 'user', 'Profil Saya', null],
                        ['account.addresses', 'map-pin', 'Alamat Saya', null],
                        ['account.orders', 'clipboard', 'Riwayat Pesanan', null],
                        ['account.wishlist', 'heart', 'Wishlist', null],
                        ['account.messages', 'mail', 'Pesan & Bantuan', $unreadReplies],
                        ['cart.index', 'cart', 'Keranjang', null],
                    ];
                @endphp

                <nav class="mt-3 space-y-1">
                    @foreach ($menu as [$route, $icon, $label, $badge])
                        <a href="{{ route($route) }}"
                            class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs($route) || (request()->routeIs('account.messages.*') && $route === 'account.messages') ? 'border border-[#4F7A63]/30 bg-[#4F7A63]/10 text-[#4F7A63]' : 'text-[#1E2B24] hover:bg-[#F6F9F7] hover:text-[#4F7A63]' }}">
                            <div class="flex items-center gap-3">
                                <x-icon :name="$icon" class="h-5 w-5 {{ request()->routeIs($route) || (request()->routeIs('account.messages.*') && $route === 'account.messages') ? 'text-[#4F7A63]' : 'text-[#5A6B61]' }}" />
                                {{ $label }}
                            </div>
                            @if ($badge && $badge > 0)
                                <span class="grid h-5 min-w-[20px] place-items-center rounded-full bg-[#E53935] px-1.5 text-[11px] font-bold text-white shadow-sm">
                                    {{ $badge }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-[#1E2B24] transition hover:bg-[#F6F9F7] hover:text-[#4F7A63]">
                            <x-icon name="cog" class="h-5 w-5 text-[#4F7A63]" /> Dashboard Admin
                        </a>
                    @endif
                </nav>

                <form action="{{ route('logout') }}" method="POST" class="mt-3 border-t border-[#D5E0D8] pt-3">
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
