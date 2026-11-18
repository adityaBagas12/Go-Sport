@extends('layouts.account')

@section('account-title', 'Profil Saya')
@section('title', 'Profil Saya - GoSport')

@section('account-content')
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Informasi Pribadi</h2>
            <p class="mt-1 text-sm text-[#5A6B61]">Perbarui nama dan nomor telepon kamu.</p>

            <form action="{{ route('account.profile.update') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Email</label>
                    <input type="email" value="{{ auth()->user()->email }}" disabled
                        class="mt-1.5 w-full cursor-not-allowed rounded-xl border border-[#D5E0D8] bg-neutral-100 px-4 py-3 text-sm text-[#5A6B61]">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <button type="submit" class="rounded-xl bg-[#4F7A63] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Ubah Password</h2>
            <p class="mt-1 text-sm text-[#5A6B61]">Gunakan password yang kuat dan mudah diingat.</p>

            <form action="{{ route('account.password.update') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Password Baru</label>
                    <input type="password" name="password" required
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <button type="submit" class="rounded-xl bg-[#2F4F3E] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#243F31]">
                    Ubah Password
                </button>
            </form>
        </div>
    </div>
@endsection
