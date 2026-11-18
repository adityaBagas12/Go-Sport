@extends('layouts.account')

@section('account-title', 'Detail Pesan & Bantuan')
@section('title', 'Detail Pesan: ' . $contactMessage->subject . ' - GoSport')

@section('account-content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('account.messages') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-[#D5E0D8] bg-white px-4 py-2 text-xs font-bold text-[#5A6B61] shadow-sm transition hover:bg-[#F6F9F7] hover:text-[#1E2B24]">
                <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke Daftar Pesan
            </a>

            <div>
                @if ($contactMessage->isReplied())
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                        <x-icon name="check-circle" class="h-4 w-4 text-emerald-600" /> Sudah Dibalas Admin
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                        ⏳ Menunggu Balasan Admin
                    </span>
                @endif
            </div>
        </div>

        {{-- Kartu Pertanyaan Pelanggan --}}
        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm sm:p-8">
            <div class="border-b border-[#D5E0D8] pb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#4F7A63]">Pertanyaan Anda</span>
                <h2 class="mt-1 text-2xl font-extrabold text-[#1E2B24]">{{ $contactMessage->subject }}</h2>
                <p class="mt-1 text-xs text-[#5A6B61]">
                    Dikirim pada {{ $contactMessage->created_at->translatedFormat('d F Y, H:i') }} WIB
                </p>
            </div>

            <div class="mt-5">
                <p class="text-xs font-bold uppercase tracking-wider text-[#5A6B61]">Isi Pesan:</p>
                <div class="mt-2 rounded-2xl border border-[#D5E0D8] bg-[#F6F9F7] p-5 text-sm leading-relaxed text-[#1E2B24] whitespace-pre-line">
{{ $contactMessage->message }}
                </div>
            </div>
        </div>

        {{-- Kartu Balasan Admin --}}
        @if ($contactMessage->isReplied())
            <div class="rounded-3xl border-2 border-[#4F7A63] bg-white p-6 shadow-md sm:p-8">
                <div class="flex items-center gap-3 border-b border-[#D5E0D8] pb-4">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#4F7A63] text-white shadow-sm">
                        <x-icon name="bolt" class="h-6 w-6" stroke="2" />
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-[#1E2B24]">Jawaban Tim Resmi GoSport</h3>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800">
                                Verified Support
                            </span>
                        </div>
                        <p class="text-xs text-[#5A6B61]">
                            Dibalas oleh <span class="font-semibold text-[#1E2B24]">{{ $contactMessage->repliedByUser?->name ?? 'Admin GoSport' }}</span>
                            pada {{ $contactMessage->replied_at?->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                <div class="mt-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#4F7A63]">Tanggapan Admin:</p>
                    <div class="mt-2 rounded-2xl border border-emerald-200 bg-emerald-50/60 p-5 text-sm leading-relaxed text-[#1E2B24] whitespace-pre-line font-medium">
{{ $contactMessage->admin_reply }}
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-3xl border border-amber-200 bg-amber-50/60 p-6 shadow-sm sm:p-8">
                <div class="flex items-start gap-4">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-amber-100 text-amber-700">
                        <x-icon name="clock" class="h-5 w-5" />
                    </span>
                    <div>
                        <h3 class="text-base font-extrabold text-amber-900">Pesan Sedang Ditinjau Tim GoSport</h3>
                        <p class="mt-1 text-sm text-amber-800 leading-relaxed">
                            Terima kasih atas kesabaran Anda. Admin kami sedang memproses pertanyaan Anda dan akan memberikan balasan langsung di halaman ini. Anda juga dapat mengecek kembali secara berkala.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between rounded-2xl border border-[#D5E0D8] bg-white p-5 shadow-sm">
            <p class="text-xs text-[#5A6B61]">Masih memiliki pertanyaan lain?</p>
            <a href="{{ route('pages.contact') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-[#2F4F3E] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#243F31]">
                <x-icon name="plus" class="h-4 w-4" /> Buat Pertanyaan Baru
            </a>
        </div>
    </div>
@endsection
