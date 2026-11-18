@extends('layouts.auth')

@section('title', 'Reset Password - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-[#1E2B24]">Reset Password</h1>
    <p class="mt-1 text-sm text-[#5A6B61]">Buat password baru untuk akun kamu.</p>

    <form action="{{ route('password.update') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">Email</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
        </div>
        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">Password Baru</label>
            <input type="password" name="password" required
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
        </div>
        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" required
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white focus:ring-2 focus:ring-[#4F7A63]/20">
        </div>
        <button type="submit" class="w-full rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-[#3E6350]">
            Simpan Password Baru
        </button>
    </form>
@endsection
