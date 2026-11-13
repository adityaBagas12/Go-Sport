@extends('layouts.account')

@section('account-title', 'Alamat Saya')
@section('title', 'Alamat Saya - GoSport')

@section('account-content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-neutral-900">Tambah Alamat Baru</h2>
            <form action="{{ route('account.addresses.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-neutral-700">Label Alamat</label>
                        <input type="text" name="label" value="{{ old('label', 'Rumah') }}" required
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-700">Nama Penerima</label>
                        <input type="text" name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" required
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-700">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-700">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province') }}" required
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-700">Kota / Kabupaten</label>
                        <input type="text" name="city" value="{{ old('city') }}" required
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-700">Kecamatan</label>
                        <input type="text" name="district" value="{{ old('district') }}" required
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-neutral-700">Kode Pos</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" required
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm text-neutral-600">
                            <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-neutral-300 text-brand-500 focus:ring-brand-500">
                            Jadikan alamat utama
                        </label>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-neutral-700">Alamat Lengkap</label>
                        <textarea name="full_address" rows="3" required
                            class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">{{ old('full_address') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-600">
                    Simpan Alamat
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-neutral-900">Daftar Alamat</h2>
            @forelse ($addresses as $address)
                <div class="mt-4 rounded-2xl border p-4 {{ $address->is_default ? 'border-brand-400 bg-brand-50/40' : 'border-neutral-100' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="flex items-center gap-2 text-sm font-bold text-neutral-900">
                                {{ $address->label }}
                                @if ($address->is_default)
                                    <span class="rounded-full bg-brand-500 px-2 py-0.5 text-[10px] font-bold uppercase text-white">Utama</span>
                                @endif
                            </p>
                            <p class="mt-1 text-sm text-neutral-600">{{ $address->recipient_name }} &middot; {{ $address->phone }}</p>
                            <p class="mt-1 text-sm text-neutral-500">
                                {{ $address->full_address }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            @unless ($address->is_default)
                                <form action="{{ route('account.addresses.default', $address->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="rounded-lg border border-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600 hover:bg-neutral-100">
                                        Jadikan Utama
                                    </button>
                                </form>
                            @endunless
                            <form action="{{ route('account.addresses.destroy', $address->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-500 hover:bg-red-50">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="mt-4 rounded-2xl bg-neutral-50 py-10 text-center text-sm text-neutral-500">Belum ada alamat tersimpan.</p>
            @endforelse
        </div>
    </div>
@endsection
