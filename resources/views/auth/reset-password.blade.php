@extends('layouts.auth')

@section('title', 'Reset Password - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-neutral-900">Reset Password</h1>
    <p class="mt-1 text-sm text-neutral-500">Buat password baru untuk akun kamu.</p>

    <form action="{{ route('password.update') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="text-sm font-semibold text-neutral-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
        </div>
        <div>
            <label class="text-sm font-semibold text-neutral-700">Password Baru</label>
            <input type="password" name="password" required
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
        </div>
        <div>
            <label class="text-sm font-semibold text-neutral-700">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" required
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
        </div>
        <button type="submit" class="w-full rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600">
            Simpan Password Baru
        </button>
    </form>
@endsection
