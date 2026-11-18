@extends('layouts.account')

@section('account-title', 'Alamat Saya')
@section('title', 'Alamat Saya - GoSport')

@section('account-content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Tambah Alamat Baru</h2>
            <form action="{{ route('account.addresses.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-[#1E2B24]">Label Alamat</label>
                        <input type="text" name="label" value="{{ old('label', 'Rumah') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#1E2B24]">Nama Penerima</label>
                        <input type="text" name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#1E2B24]">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#1E2B24]">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#1E2B24]">Kota / Kabupaten</label>
                        <input type="text" name="city" value="{{ old('city') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#1E2B24]">Kecamatan</label>
                        <input type="text" name="district" value="{{ old('district') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#1E2B24]">Kode Pos</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm text-[#1E2B24]">
                            <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-[#D5E0D8] text-[#4F7A63] focus:ring-[#4F7A63]">
                            Jadikan alamat utama
                        </label>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-[#1E2B24]">Alamat Lengkap</label>
                        <textarea name="full_address" rows="3" required
                            class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">{{ old('full_address') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="rounded-xl bg-[#4F7A63] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                    Simpan Alamat
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Daftar Alamat</h2>
            @forelse ($addresses as $address)
                <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-[#1E2B24]">{{ $address->label }}</h3>
                            @if ($address->is_default)
                                <span class="rounded-full bg-[#4F7A63]/10 px-2.5 py-0.5 text-xs font-bold text-[#4F7A63]">Utama</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @unless ($address->is_default)
                                <form action="{{ route('account.addresses.default', $address) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-[#4F7A63] hover:underline">Jadikan Utama</button>
                                </form>
                            @endunless
                            <form action="{{ route('account.addresses.destroy', $address) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-[#E53935] hover:underline">Hapus</button>
                            </form>
                        </div>
                    </div>
                    <p class="mt-2 text-sm font-bold text-[#1E2B24]">{{ $address->recipient_name }} &middot; {{ $address->phone }}</p>
                    <p class="mt-1 text-sm text-[#5A6B61]">{{ $address->full_address }}</p>
                    <p class="mt-0.5 text-xs text-[#5A6B61]">{{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                </div>
            @empty
                <div class="rounded-3xl border border-[#D5E0D8] bg-white py-12 text-center text-sm text-[#5A6B61] shadow-sm">
                    Belum ada alamat tersimpan.
                </div>
            @endforelse
        </div>
    </div>
@endsection
