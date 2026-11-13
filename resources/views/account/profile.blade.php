@extends('layouts.account')

@section('account-title', 'Profil Saya')
@section('title', 'Profil Saya - GoSport')

@section('account-content')
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#0A2A5E]">Informasi Pribadi</h2>
            <p class="mt-1 text-sm text-neutral-500">Perbarui nama dan nomor telepon kamu.</p>

            <form action="{{ route('account.profile.update') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-semibold text-[#10213F]">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#10213F]">Email</label>
                    <input type="email" value="{{ auth()->user()->email }}" disabled
                        class="mt-1.5 w-full cursor-not-allowed rounded-xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-sm text-neutral-500">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#10213F]">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                        class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                </div>
                <button type="submit" class="rounded-xl bg-[#0A6CFF] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#3D9BFF]">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#0A2A5E]">Ubah Password</h2>
            <p class="mt-1 text-sm text-neutral-500">Gunakan password yang kuat dan mudah diingat.</p>

            <form action="{{ route('account.password.update') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-semibold text-[#10213F]">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                        class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#10213F]">Password Baru</label>
                    <input type="password" name="password" required
                        class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#10213F]">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                        class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                </div>
                <button type="submit" class="rounded-xl bg-[#0A2A5E] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#0A6CFF]">
                    Ubah Password
                </button>
            </form>
        </div>
    </div>
@endsection
