@extends('layouts.auth')

@section('title', 'Lupa Password - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-neutral-900">Lupa Password?</h1>
    <p class="mt-1 text-sm text-neutral-500">Masukkan email terdaftar dan kami akan mengirim tautan untuk reset password.</p>

    <form action="{{ route('password.email') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="text-sm font-semibold text-neutral-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
        </div>
        <button type="submit" class="w-full rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600">
            Kirim Tautan Reset
        </button>
        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 text-sm font-semibold text-neutral-600 hover:text-brand-600">
            <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke halaman masuk
        </a>
    </form>
@endsection
