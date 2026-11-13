@php
    $footerCategories = \App\Models\Category::orderBy('name')->take(6)->get();
@endphp

<footer class="mt-16 bg-neutral-900 text-neutral-300">
    <div class="container-page grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-4">
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-500 text-neutral-900">
                    <x-icon name="bolt" class="h-5 w-5" stroke="2" />
                </span>
                <span class="text-xl font-extrabold tracking-tight text-white">Go<span class="text-brand-500">Sport</span></span>
            </a>
            <p class="mt-4 text-sm leading-relaxed text-neutral-400">
                GoSport adalah toko online perlengkapan olahraga untuk pria, wanita, dan anak. Produk original,
                harga terbaik, dan pengiriman cepat ke seluruh Indonesia.
            </p>
            <div class="mt-5 flex gap-3">
                <a href="#" class="grid h-10 w-10 place-items-center rounded-xl bg-neutral-800 text-neutral-300 transition hover:bg-brand-500 hover:text-white" aria-label="Facebook"><x-icon name="facebook" class="h-5 w-5" /></a>
                <a href="#" class="grid h-10 w-10 place-items-center rounded-xl bg-neutral-800 text-neutral-300 transition hover:bg-brand-500 hover:text-white" aria-label="Instagram"><x-icon name="instagram" class="h-5 w-5" /></a>
                <a href="#" class="grid h-10 w-10 place-items-center rounded-xl bg-neutral-800 text-neutral-300 transition hover:bg-brand-500 hover:text-white" aria-label="YouTube"><x-icon name="youtube" class="h-5 w-5" /></a>
                <a href="#" class="grid h-10 w-10 place-items-center rounded-xl bg-neutral-800 text-neutral-300 transition hover:bg-brand-500 hover:text-white" aria-label="WhatsApp"><x-icon name="whatsapp" class="h-5 w-5" /></a>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Kategori</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                @foreach ($footerCategories as $category)
                    <li><a href="{{ route('categories.show', $category->slug) }}" class="transition hover:text-brand-500">{{ $category->name }}</a></li>
                @endforeach
                <li><a href="{{ route('categories.index') }}" class="font-semibold text-brand-500 hover:text-brand-400">Lihat Semua</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Bantuan</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('pages.about') }}" class="transition hover:text-brand-500">Tentang Kami</a></li>
                <li><a href="{{ route('pages.contact') }}" class="transition hover:text-brand-500">Kontak</a></li>
                <li><a href="{{ route('account.orders') }}" class="transition hover:text-brand-500">Lacak Pesanan</a></li>
                <li><a href="{{ route('pages.promo') }}" class="transition hover:text-brand-500">Promo & Diskon</a></li>
                <li><a href="{{ route('account.dashboard') }}" class="transition hover:text-brand-500">Akun Saya</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Hubungi Kami</h3>
            <ul class="mt-4 space-y-3 text-sm">
                <li class="flex gap-3">
                    <x-icon name="map-pin" class="mt-0.5 h-5 w-5 shrink-0 text-brand-500" />
                    <span>Jl. W.R. Supratman, Kandang Limun, Kec. Muara Bangka Hulu, Kota Bengkulu</span>
                </li>
                <li class="flex gap-3">
                    <x-icon name="phone" class="mt-0.5 h-5 w-5 shrink-0 text-brand-500" />
                    <span>0800-1-GOSPORT<br>0812-3456-7890</span>
                </li>
                <li class="flex gap-3">
                    <x-icon name="mail" class="mt-0.5 h-5 w-5 shrink-0 text-brand-500" />
                    <span>cs@gosport.id</span>
                </li>
                <li class="flex gap-3">
                    <x-icon name="clock" class="mt-0.5 h-5 w-5 shrink-0 text-brand-500" />
                    <span>Senin - Minggu, 08.00 - 21.00 WIB</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-neutral-800">
        <div class="container-page flex flex-col items-center justify-between gap-3 py-5 text-xs text-neutral-500 sm:flex-row">
            <p>&copy; {{ date('Y') }} GoSport. Seluruh hak cipta dilindungi.</p>
            <p>Metode Pembayaran: Transfer Bank &middot; QRIS &middot; COD</p>
        </div>
    </div>
</footer>
