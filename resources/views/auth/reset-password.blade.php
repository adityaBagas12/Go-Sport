@extends('layouts.auth')

@section('title', 'Reset Password - GoSport')

@section('content')
    <h1 class="text-2xl font-extrabold text-[#0A2A5E]">Reset Password</h1>
    <p class="mt-1 text-sm text-neutral-500">Buat password baru untuk akun kamu.</p>

    <form action="{{ route('password.update') }}" method="POST" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="text-sm font-semibold text-[#10213F]">Email</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required
                class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white focus:ring-2 focus:ring-[#8CC4FF]/40">
        </div>
        <div>
            <label class="text-sm font-semibold text-[#10213F]">Password Baru</label>
            <input type="password" name="password" required
                class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white focus:ring-2 focus:ring-[#8CC4FF]/40">
        </div>
        <div>
            <label class="text-sm font-semibold text-[#10213F]">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" required
                class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white focus:ring-2 focus:ring-[#8CC4FF]/40">
        </div>
        <button type="submit" class="w-full rounded-xl bg-[#0A6CFF] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-[#3D9BFF]">
            Simpan Password Baru
        </button>
    </form>
@endsection
