@extends('layouts.account')

@section('account-title', 'Alamat Saya')
@section('title', 'Alamat Saya - GoSport')

@section('account-content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#0A2A5E]">Tambah Alamat Baru</h2>
            <form action="{{ route('account.addresses.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-[#10213F]">Label Alamat</label>
                        <input type="text" name="label" value="{{ old('label', 'Rumah') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#10213F]">Nama Penerima</label>
                        <input type="text" name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#10213F]">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#10213F]">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#10213F]">Kota / Kabupaten</label>
                        <input type="text" name="city" value="{{ old('city') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#10213F]">Kecamatan</label>
                        <input type="text" name="district" value="{{ old('district') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-[#10213F]">Kode Pos</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" required
                            class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm text-[#10213F]">
                            <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-[#8CC4FF] text-[#0A6CFF] focus:ring-[#0A6CFF]">
                            Jadikan alamat utama
                        </label>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-[#10213F]">Alamat Lengkap</label>
                        <textarea name="full_address" rows="3" required
                            class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">{{ old('full_address') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="rounded-xl bg-[#0A6CFF] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#3D9BFF]">
                    Simpan Alamat
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#0A2A5E]">Daftar Alamat</h2>
            @forelse ($addresses as $address)
                <div class="mt-4 rounded-2xl border p-4 {{ $address->is_default ? 'border-[#0A6CFF] bg-[#E6F1FF]/50' : 'border-[#8CC4FF]/40 bg-white' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="flex items-center gap-2 text-sm font-bold text-[#10213F]">
                                {{ $address->label }}
                                @if ($address->is_default)
                                    <span class="rounded-full bg-[#0A6CFF] px-2 py-0.5 text-[10px] font-bold uppercase text-white shadow-sm">Utama</span>
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
                                    <button type="submit" class="rounded-lg border border-[#8CC4FF] px-3 py-2 text-xs font-bold text-[#0A6CFF] transition hover:bg-[#E6F1FF]">
                                        Jadikan Utama
                                    </button>
                                </form>
                            @endunless
                            <form action="{{ route('account.addresses.destroy', $address->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-[#E53935]/40 px-3 py-2 text-xs font-bold text-[#E53935] transition hover:bg-[#E53935]/10">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="mt-4 rounded-2xl border border-[#8CC4FF]/30 bg-[#E6F1FF] py-10 text-center text-sm text-[#10213F]">Belum ada alamat tersimpan.</p>
            @endforelse
        </div>
    </div>
@endsection
