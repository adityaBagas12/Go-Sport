@extends('layouts.auth')

@section('title', 'Lupa Password - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-[#1E2B24]">Lupa Password?</h1>
    <p class="mt-1 text-sm text-[#5A6B61]">Masukkan email terdaftar dan kami akan mengirim tautan untuk reset password.</p>

    <form action="{{ route('password.email') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
        </div>
        <button type="submit" class="w-full rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-[#3E6350]">
            Kirim Tautan Reset
        </button>
        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 text-sm font-bold text-[#4F7A63] hover:underline">
            <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke halaman masuk
        </a>
    </form>
@endsection
