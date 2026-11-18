@extends('layouts.app')

@section('title', 'Kontak - GoSport')

@section('content')
    <div class="border-b border-[#D5E0D8] bg-white">
        <div class="container-page py-10 text-center">
            <p class="text-sm font-bold uppercase tracking-wider text-[#4F7A63]">Kontak</p>
            <h1 class="mt-2 text-3xl font-extrabold text-[#1E2B24] sm:text-4xl">Hubungi GoSport</h1>
            <p class="mx-auto mt-3 max-w-xl text-[#5A6B61]">
                Ada pertanyaan tentang produk, pesanan, atau pengiriman? Tim kami siap membantu.
            </p>
        </div>
    </div>

    <div class="container-page grid gap-10 py-12 lg:grid-cols-[1fr_1.2fr]">
        <div class="space-y-4">
            @foreach ([
                ['map-pin', 'Alamat Toko', 'Jl. W.R. Supratman, Kandang Limun, Kec. Muara Bangka Hulu, Kota Bengkulu'],
                ['phone', 'Telepon / WhatsApp', '0800-1-GOSPORT (0800-1-4677678) / 0812-3456-7890'],
                ['mail', 'Email', 'cs@gosport.id / support@gosport.id'],
                ['clock', 'Jam Operasional', 'Senin - Minggu, 08.00 - 21.00 WIB'],
            ] as [$icon, $title, $desc])
                <div class="flex items-start gap-4 rounded-2xl border border-[#D5E0D8] bg-white p-5 shadow-sm">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-[#4F7A63]/10 text-[#4F7A63]">
                        <x-icon :name="$icon" class="h-6 w-6" />
                    </span>
                    <div>
                        <p class="text-sm font-bold text-[#1E2B24]">{{ $title }}</p>
                        <p class="mt-1 text-sm text-[#5A6B61]">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach

            <div class="flex gap-3">
                @foreach (['facebook', 'instagram', 'youtube', 'whatsapp'] as $social)
                    <a href="#" class="grid h-11 w-11 place-items-center rounded-xl bg-[#2F4F3E] text-white transition hover:bg-[#4F7A63]">
                        <x-icon :name="$social" class="h-5 w-5" />
                    </a>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-7 shadow-sm">
            <h2 class="text-xl font-extrabold text-[#1E2B24]">Kirim Pesan</h2>
            <form class="mt-6 space-y-4" onsubmit="event.preventDefault(); this.reset(); alert('Pesan kamu sudah terkirim. Tim GoSport akan segera menghubungi kamu.');">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-[#1E2B24]">Nama Lengkap</label>
                        <input type="text" required class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#1E2B24]">Email</label>
                        <input type="email" required class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Subjek</label>
                    <input type="text" required class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Pesan</label>
                    <textarea rows="5" required class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white"></textarea>
                </div>
                <button type="submit" class="w-full rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>
@endsection
