<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') - GoSport</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-[#F4F8FF] text-[#10213F]">
    <div class="flex min-h-screen">
        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full overflow-y-auto border-r border-[#8CC4FF]/25 bg-[#0A2A5E] text-white transition-transform lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
            <div class="flex h-16 items-center gap-2 border-b border-[#8CC4FF]/20 px-5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#0A6CFF] text-white shadow-sm">
                    <x-icon name="bolt" class="h-5 w-5" stroke="2" />
                </span>
                <div>
                    <p class="text-base font-extrabold leading-none text-white">Go<span class="text-[#FF7A00]">Sport</span></p>
                    <p class="text-[11px] text-blue-200/70">Admin Panel</p>
                </div>
            </div>

            @php
                $adminMenu = [
                    ['admin.dashboard', 'grid', 'Dashboard'],
                    ['admin.products.index', 'box', 'Produk'],
                    ['admin.categories.index', 'tag', 'Kategori'],
                    ['admin.orders.index', 'clipboard', 'Pesanan'],
                    ['admin.customers.index', 'users', 'Customer'],
                    ['admin.coupons.index', 'ticket', 'Voucher & Promo'],
                ];
            @endphp

            <nav class="space-y-1 p-4">
                @foreach ($adminMenu as [$route, $icon, $label])
                    <a href="{{ route($route) }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs(str_replace('.index', '.*', $route)) || request()->routeIs($route) ? 'bg-[#0A6CFF] text-white shadow-sm' : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">
                        <x-icon :name="$icon" class="h-5 w-5" />
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-4 border-t border-[#8CC4FF]/20 p-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-[#E53935] transition hover:bg-[#E53935]/10">
                        <x-icon name="logout" class="h-5 w-5" /> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-40 flex h-16 items-center gap-4 border-b border-[#8CC4FF]/30 bg-white px-4 lg:px-8">
                <button data-sidebar-toggle class="grid h-10 w-10 place-items-center rounded-xl text-[#0A2A5E] hover:bg-[#E6F1FF] lg:hidden">
                    <x-icon name="menu" class="h-6 w-6" />
                </button>
                <h1 class="text-lg font-extrabold text-[#0A2A5E]">@yield('admin-title', 'Dashboard')</h1>
                <div class="ml-auto flex items-center gap-3">
                    <a href="{{ route('home') }}" class="hidden rounded-xl border border-[#8CC4FF] bg-white px-4 py-2 text-sm font-bold text-[#0A6CFF] transition hover:bg-[#E6F1FF] sm:block">
                        Lihat Toko
                    </a>
                    <div class="flex items-center gap-3 rounded-xl border border-[#8CC4FF]/40 bg-[#E6F1FF] px-3 py-2">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#0A6CFF] text-sm font-bold text-white shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <div class="hidden leading-tight sm:block">
                            <p class="text-sm font-bold text-[#10213F]">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-[#10213F]/70">Administrator</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-8">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('admin-sidebar')?.classList.toggle('-translate-x-full');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
