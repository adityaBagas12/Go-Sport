@extends('layouts.auth')

@section('title', 'Daftar - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-[#1E2B24]">Buat Akun Baru</h1>
    <p class="mt-1 text-sm text-[#5A6B61]">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-[#4F7A63] hover:underline">Masuk di sini</a></p>

    <form action="{{ route('register') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
        </div>
        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
        </div>
        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">Nomor Telepon</label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-[#1E2B24]">Password</label>
                <div class="relative mt-1.5">
                    <input type="password" name="password" id="register-password" required
                        class="w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 pr-12 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
                    <button type="button" data-password-toggle="register-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#5A6B61] hover:text-[#1E2B24]">
                        <x-icon name="eye" class="h-5 w-5" />
                    </button>
                </div>
            </div>
            <div>
                <label class="text-sm font-semibold text-[#1E2B24]">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
            </div>
        </div>
        <button type="submit" class="w-full rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-[#3E6350]">
            Daftar Sekarang
        </button>
        <p class="text-center text-xs text-[#5A6B61]">Dengan mendaftar, kamu menyetujui syarat & ketentuan GoSport.</p>
    </form>
@endsection
