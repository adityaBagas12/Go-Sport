@extends('layouts.app')

@section('title', 'Tentang Kami - GoSport')

@section('content')
    <section class="border-b border-[#243F31] bg-[#2F4F3E] text-white">
        <div class="container-page py-16 text-center">
            <p class="text-sm font-bold uppercase tracking-wider text-[#DCE8DF]">Tentang Kami</p>
            <h1 class="mt-3 text-3xl font-black text-white !text-white sm:text-4xl" style="color: #ffffff !important;">GoSport, Partner Olahragamu</h1>
            <p class="mx-auto mt-4 max-w-2xl text-emerald-100/80">
                Sejak 2018, GoSport hadir untuk memudahkan masyarakat Indonesia mendapatkan perlengkapan olahraga
                berkualitas dengan harga yang terjangkau.
            </p>
        </div>
    </section>

    <div class="container-page py-14">
        <div class="grid items-center gap-10 lg:grid-cols-2">
            <div class="overflow-hidden rounded-3xl border border-[#D5E0D8] shadow-lg">
                <img src="https://img.freepik.com/premium-photo/international-olympic-day-diverse-athletes-competing-various-sports_1230681-2459.jpg?w=1060" alt="Toko GoSport" class="h-full w-full object-cover">
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-[#1E2B24]">Misi Kami</h2>
                <p class="mt-4 leading-relaxed text-[#1E2B24]">
                    Kami percaya bahwa setiap orang berhak mendapatkan perlengkapan olahraga terbaik. GoSport menyediakan
                    ribuan produk original dari brand terpercaya, mulai dari sepatu, jersey, bola, raket, hingga peralatan
                    fitness, untuk pria, wanita, dan anak-anak.
                </p>
                <p class="mt-4 leading-relaxed text-[#5A6B61]">
                    Dengan pengiriman cepat ke seluruh Indonesia, layanan pelanggan 24/7, dan jaminan retur 7 hari,
                    kami ingin pengalaman belanja olahraga kamu jadi menyenangkan.
                </p>

                <div class="mt-8 grid grid-cols-2 gap-4">
                    @foreach ([['1.500+', 'Produk'], ['50rb+', 'Pelanggan'], ['8', 'Kategori'], ['4.9/5', 'Rating']] as [$value, $label])
                        <div class="rounded-2xl border border-[#D5E0D8] bg-white p-5 shadow-sm">
                            <p class="text-2xl font-black text-[#4F7A63]">{{ $value }}</p>
                            <p class="text-sm text-[#5A6B61]">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-16 grid gap-6 sm:grid-cols-3">
            @foreach ([
                ['shield', 'Produk Original', 'Semua produk kami jamin keasliannya dan bergaransi resmi.'],
                ['truck', 'Pengiriman Cepat', 'Dikirim hari yang sama untuk pesanan sebelum jam 15.00 WIB.'],
                ['headset', 'Layanan 24/7', 'Tim customer service siap membantu kapan pun kamu butuh.'],
            ] as [$icon, $title, $desc])
                <div class="rounded-3xl border border-[#D5E0D8] bg-white p-7 text-center shadow-sm">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#4F7A63]/10 text-[#4F7A63]">
                        <x-icon :name="$icon" class="h-7 w-7" />
                    </span>
                    <h3 class="mt-4 text-lg font-bold text-[#1E2B24]">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-[#5A6B61]">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
