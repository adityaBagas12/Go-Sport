@extends('layouts.auth')

@section('title', 'Lupa Password - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-[#0A2A5E]">Lupa Password?</h1>
    <p class="mt-1 text-sm text-neutral-500">Masukkan email terdaftar dan kami akan mengirim tautan untuk reset password.</p>

    <form action="{{ route('password.email') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <div>
            <label class="text-sm font-semibold text-[#10213F]">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white focus:ring-2 focus:ring-[#8CC4FF]/40">
        </div>
        <button type="submit" class="w-full rounded-xl bg-[#0A6CFF] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-[#3D9BFF]">
            Kirim Tautan Reset
        </button>
        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 text-sm font-bold text-[#0A6CFF] hover:underline">
            <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke halaman masuk
        </a>
    </form>
@endsection
