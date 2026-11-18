<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk - GoSport')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F6F9F7] text-[#1E2B24]">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-[#2F4F3E] p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-0 opacity-15" style="background-image: linear-gradient(115deg, transparent 0 45%, #4F7A63 45% 47%, transparent 47%), linear-gradient(115deg, transparent 0 60%, #4F7A63 60% 62%, transparent 62%);"></div>
            <div class="absolute -right-20 top-20 h-72 w-72 rounded-full bg-[#4F7A63]/20 blur-3xl"></div>

            <a href="{{ route('home') }}" class="relative inline-flex items-center gap-2">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#4F7A63] text-white shadow-sm">
                    <x-icon name="bolt" class="h-5 w-5" stroke="2" />
                </span>
                <span class="text-2xl font-extrabold tracking-tight text-white">Go<span class="text-[#DCE8DF]">Sport</span></span>
            </a>

            <div class="relative">
                <h2 class="text-4xl font-black leading-tight text-white !text-white" style="color: #ffffff !important;">Lengkapi Perlengkapan Olahragamu Bersama GoSport</h2>
                <p class="mt-4 max-w-md text-emerald-100/80">Temukan produk olahraga berkualitas dengan harga terbaik untuk pria, wanita, dan anak.</p>
                <div class="mt-8 flex flex-wrap gap-6">
                    @foreach ([['truck', 'Gratis Ongkir'], ['shield', '100% Original'], ['refresh', 'Retur 7 Hari']] as [$icon, $label])
                        <div class="flex items-center gap-2 text-sm font-semibold text-white">
                            <x-icon :name="$icon" class="h-5 w-5 text-[#DCE8DF]" /> {{ $label }}
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="relative text-xs text-emerald-200/60">&copy; {{ date('Y') }} GoSport. Seluruh hak cipta dilindungi.</p>
        </div>

        <div class="flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md rounded-3xl border border-[#D5E0D8] bg-white p-8 shadow-sm">
                <div class="mb-8 text-center lg:hidden">
                    <x-logo class="justify-center" />
                </div>
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
