@extends('layouts.auth')

@section('title', 'Daftar - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-neutral-900">Buat Akun Baru</h1>
    <p class="mt-1 text-sm text-neutral-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Masuk di sini</a></p>

    <form action="{{ route('register') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="text-sm font-semibold text-neutral-700">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
        </div>
        <div>
            <label class="text-sm font-semibold text-neutral-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
        </div>
        <div>
            <label class="text-sm font-semibold text-neutral-700">Nomor Telepon</label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-neutral-700">Password</label>
                <div class="relative mt-1.5">
                    <input type="password" name="password" id="register-password" required
                        class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 pr-12 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                    <button type="button" data-password-toggle="register-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-700">
                        <x-icon name="eye" class="h-5 w-5" />
                    </button>
                </div>
            </div>
            <div>
                <label class="text-sm font-semibold text-neutral-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
            </div>
        </div>
        <button type="submit" class="w-full rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600">
            Daftar Sekarang
        </button>
        <p class="text-center text-xs text-neutral-400">Dengan mendaftar, kamu menyetujui syarat & ketentuan GoSport.</p>
    </form>
@endsection
