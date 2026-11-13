@php
    $footerCategories = \App\Models\Category::orderBy('name')->take(6)->get();
@endphp

<footer class="mt-16 border-t border-[#8CC4FF]/30 bg-[#0A2A5E] text-neutral-200">
    <div class="container-page grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-4">
        <div>
            <x-logo />
            <p class="mt-4 text-sm leading-relaxed text-[#E6F1FF]/80">
                GoSport adalah toko online perlengkapan olahraga untuk pria, wanita, dan anak. Produk original,
                harga terbaik, dan pengiriman cepat ke seluruh Indonesia.
            </p>
            <div class="mt-5 flex gap-3">
                <a href="#" class="grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-[#8CC4FF] transition hover:bg-[#0A6CFF] hover:text-white" aria-label="Facebook"><x-icon name="facebook" class="h-5 w-5" /></a>
                <a href="#" class="grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-[#8CC4FF] transition hover:bg-[#0A6CFF] hover:text-white" aria-label="Instagram"><x-icon name="instagram" class="h-5 w-5" /></a>
                <a href="#" class="grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-[#8CC4FF] transition hover:bg-[#0A6CFF] hover:text-white" aria-label="YouTube"><x-icon name="youtube" class="h-5 w-5" /></a>
                <a href="#" class="grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-[#8CC4FF] transition hover:bg-[#0A6CFF] hover:text-white" aria-label="WhatsApp"><x-icon name="whatsapp" class="h-5 w-5" /></a>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Kategori</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                @foreach ($footerCategories as $category)
                    <li><a href="{{ route('categories.show', $category->slug) }}" class="text-[#E6F1FF]/80 transition hover:text-[#3D9BFF]">{{ $category->name }}</a></li>
                @endforeach
                <li><a href="{{ route('categories.index') }}" class="font-bold text-[#FF7A00] transition hover:text-[#ff8f26]">Lihat Semua</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Bantuan</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('pages.about') }}" class="text-[#E6F1FF]/80 transition hover:text-[#3D9BFF]">Tentang Kami</a></li>
                <li><a href="{{ route('pages.contact') }}" class="text-[#E6F1FF]/80 transition hover:text-[#3D9BFF]">Kontak</a></li>
                <li><a href="{{ route('account.orders') }}" class="text-[#E6F1FF]/80 transition hover:text-[#3D9BFF]">Lacak Pesanan</a></li>
                <li><a href="{{ route('pages.promo') }}" class="text-[#E6F1FF]/80 transition hover:text-[#3D9BFF]">Promo & Diskon</a></li>
                <li><a href="{{ route('account.dashboard') }}" class="text-[#E6F1FF]/80 transition hover:text-[#3D9BFF]">Akun Saya</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Hubungi Kami</h3>
            <ul class="mt-4 space-y-3 text-sm text-[#E6F1FF]/80">
                <li class="flex gap-3">
                    <x-icon name="map-pin" class="mt-0.5 h-5 w-5 shrink-0 text-[#3D9BFF]" />
                    <span>Jl. W.R. Supratman, Kandang Limun, Kec. Muara Bangka Hulu, Kota Bengkulu</span>
                </li>
                <li class="flex gap-3">
                    <x-icon name="phone" class="mt-0.5 h-5 w-5 shrink-0 text-[#3D9BFF]" />
                    <span>0800-1-GOSPORT<br>0812-3456-7890</span>
                </li>
                <li class="flex gap-3">
                    <x-icon name="mail" class="mt-0.5 h-5 w-5 shrink-0 text-[#3D9BFF]" />
                    <span>cs@gosport.id</span>
                </li>
                <li class="flex gap-3">
                    <x-icon name="clock" class="mt-0.5 h-5 w-5 shrink-0 text-[#3D9BFF]" />
                    <span>Senin - Minggu, 08.00 - 21.00 WIB</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-[#8CC4FF]/20 bg-[#071E43]">
        <div class="container-page flex flex-col items-center justify-between gap-3 py-5 text-xs text-[#8CC4FF] sm:flex-row">
            <p>&copy; {{ date('Y') }} GoSport. Seluruh hak cipta dilindungi.</p>
            <p>Metode Pembayaran: Midtrans (Virtual Account, QRIS, GoPay, Kartu Kredit) &middot; COD</p>
        </div>
    </div>
</footer>
