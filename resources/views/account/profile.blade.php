@extends('layouts.account')

@section('account-title', 'Profil Saya')
@section('title', 'Profil Saya - GoSport')

@section('account-content')
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-neutral-900">Informasi Pribadi</h2>
            <p class="mt-1 text-sm text-neutral-500">Perbarui nama dan nomor telepon kamu.</p>

            <form action="{{ route('account.profile.update') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Email</label>
                    <input type="email" value="{{ auth()->user()->email }}" disabled
                        class="mt-1.5 w-full cursor-not-allowed rounded-xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-sm text-neutral-500">
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                </div>
                <button type="submit" class="rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-600">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-neutral-900">Ubah Password</h2>
            <p class="mt-1 text-sm text-neutral-500">Gunakan password yang kuat dan mudah diingat.</p>

            <form action="{{ route('account.password.update') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Password Baru</label>
                    <input type="password" name="password" required
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                </div>
                <button type="submit" class="rounded-xl bg-neutral-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-500">
                    Ubah Password
                </button>
            </form>
        </div>
    </div>
@endsection
