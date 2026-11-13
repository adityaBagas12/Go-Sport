@extends('layouts.auth')

@section('title', 'Masuk - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-[#0A2A5E]">Masuk ke Akun Kamu</h1>
    <p class="mt-1 text-sm text-neutral-500">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-[#0A6CFF] hover:underline">Daftar sekarang</a></p>

    <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="text-sm font-semibold text-[#10213F]">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white focus:ring-2 focus:ring-[#8CC4FF]/40">
        </div>
        <div>
            <div class="flex items-center justify-between">
                <label class="text-sm font-semibold text-[#10213F]">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs font-bold text-[#0A6CFF] hover:underline">Lupa password?</a>
            </div>
            <div class="relative mt-1.5">
                <input type="password" name="password" id="login-password" required
                    class="w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 pr-12 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white focus:ring-2 focus:ring-[#8CC4FF]/40">
                <button type="button" data-password-toggle="login-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-700">
                    <x-icon name="eye" class="h-5 w-5" />
                </button>
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm text-[#10213F]">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[#8CC4FF] text-[#0A6CFF] focus:ring-[#0A6CFF]">
            Ingat saya
        </label>
        <button type="submit" class="w-full rounded-xl bg-[#0A6CFF] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-[#3D9BFF]">
            Masuk
        </button>
    </form>
@endsection
