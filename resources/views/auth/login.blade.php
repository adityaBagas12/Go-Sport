@extends('layouts.auth')

@section('title', 'Masuk - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-neutral-900">Masuk ke Akun Kamu</h1>
    <p class="mt-1 text-sm text-neutral-500">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">Daftar sekarang</a></p>

    <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="text-sm font-semibold text-neutral-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
        </div>
        <div>
            <div class="flex items-center justify-between">
                <label class="text-sm font-semibold text-neutral-700">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-600 hover:underline">Lupa password?</a>
            </div>
            <div class="relative mt-1.5">
                <input type="password" name="password" id="login-password" required
                    class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 pr-12 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                <button type="button" data-password-toggle="login-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-700">
                    <x-icon name="eye" class="h-5 w-5" />
                </button>
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm text-neutral-600">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-neutral-300 text-brand-500 focus:ring-brand-500">
            Ingat saya
        </label>
        <button type="submit" class="w-full rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600">
            Masuk
        </button>
    </form>


@endsection
