@extends('layouts.auth')

@section('title', 'Masuk - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-[#1E2B24]">Masuk ke Akun Kamu</h1>
    <p class="mt-1 text-sm text-[#5A6B61]">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-[#4F7A63] hover:underline">Daftar sekarang</a></p>

    <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
        </div>
        <div>
            <div class="flex items-center justify-between">
                <label class="text-sm font-semibold text-[#1E2B24]">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs font-bold text-[#4F7A63] hover:underline">Lupa password?</a>
            </div>
            <div class="relative mt-1.5">
                <input type="password" name="password" id="login-password" required
                    class="w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 pr-12 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
                <button type="button" data-password-toggle="login-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#5A6B61] hover:text-[#1E2B24]">
                    <x-icon name="eye" class="h-5 w-5" />
                </button>
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm text-[#1E2B24]">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[#D5E0D8] text-[#4F7A63] focus:ring-[#4F7A63]">
            Ingat saya
        </label>
        <button type="submit" class="w-full rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-[#3E6350]">
            Masuk
        </button>
    </form>
@endsection
