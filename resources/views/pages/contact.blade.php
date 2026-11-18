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
            <p class="mt-1 text-sm text-[#5A6B61]">Silakan isi formulir di bawah ini, admin kami akan segera merespons ke email Anda.</p>

            @if (session('success'))
                <div class="mt-4 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                    <div>
                        <p class="text-sm font-bold">Pesan Berhasil Terkirim!</p>
                        <p class="mt-0.5 text-xs text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form class="mt-6 space-y-4" method="POST" action="{{ route('pages.contact.send') }}">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="text-sm font-semibold text-[#1E2B24]">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" required value="{{ old('name', auth()->user()?->name) }}" placeholder="Nama Anda"
                            class="mt-1.5 w-full rounded-xl border @error('name') border-red-400 bg-red-50 @else border-[#D5E0D8] bg-[#F6F9F7] @enderror px-4 py-3 text-sm text-[#1E2B24] outline-none transition focus:border-[#4F7A63] focus:bg-white">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="text-sm font-semibold text-[#1E2B24]">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required value="{{ old('email', auth()->user()?->email) }}" placeholder="nama@email.com"
                            class="mt-1.5 w-full rounded-xl border @error('email') border-red-400 bg-red-50 @else border-[#D5E0D8] bg-[#F6F9F7] @enderror px-4 py-3 text-sm text-[#1E2B24] outline-none transition focus:border-[#4F7A63] focus:bg-white">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="subject" class="text-sm font-semibold text-[#1E2B24]">Subjek <span class="text-red-500">*</span></label>
                    <input type="text" id="subject" name="subject" required value="{{ old('subject') }}" placeholder="Contoh: Pertanyaan Ukuran Jersey / Kerjasama"
                        class="mt-1.5 w-full rounded-xl border @error('subject') border-red-400 bg-red-50 @else border-[#D5E0D8] bg-[#F6F9F7] @enderror px-4 py-3 text-sm text-[#1E2B24] outline-none transition focus:border-[#4F7A63] focus:bg-white">
                    @error('subject')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="message" class="text-sm font-semibold text-[#1E2B24]">Pesan <span class="text-red-500">*</span></label>
                    <textarea id="message" name="message" rows="5" required placeholder="Tuliskan pertanyaan atau pesan Anda secara rinci di sini..."
                        class="mt-1.5 w-full rounded-xl border @error('message') border-red-400 bg-red-50 @else border-[#D5E0D8] bg-[#F6F9F7] @enderror px-4 py-3 text-sm text-[#1E2B24] outline-none transition focus:border-[#4F7A63] focus:bg-white">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                    <x-icon name="mail" class="h-4 w-4" />
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>
@endsection
