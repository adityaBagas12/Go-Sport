@extends('layouts.account')

@section('account-title', 'Pesan & Bantuan')
@section('title', 'Pesan & Bantuan - GoSport')

@section('account-content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-xl font-extrabold text-[#1E2B24]">Pesan & Pertanyaan Saya</h2>
                <p class="mt-1 text-sm text-[#5A6B61]">
                    Pantau status tanggapan dan jawaban resmi dari tim customer service GoSport di sini.
                </p>
            </div>
            <a href="{{ route('pages.contact') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-[#2F4F3E] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#243F31]">
                <x-icon name="plus" class="h-4 w-4" /> Kirim Pesan Baru
            </a>
        </div>

        <div class="space-y-4">
            @forelse ($messages as $msg)
                <div class="rounded-3xl border {{ $msg->isReplied() && ! $msg->customer_read_at ? 'border-[#4F7A63] bg-emerald-50/20 ring-2 ring-[#4F7A63]/20' : 'border-[#D5E0D8] bg-white' }} p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#D5E0D8] pb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-[#5A6B61]">
                                Dikirim pada {{ $msg->created_at->translatedFormat('d F Y, H:i') }} WIB
                            </span>
                        </div>

                        <div>
                            @if ($msg->isReplied())
                                @if (! $msg->customer_read_at)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow-sm">
                                        <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span> Ada Balasan Baru Admin!
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                        ✓ Sudah Dibalas Admin
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                                    ⏳ Menunggu Balasan Admin
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-base font-extrabold text-[#1E2B24]">{{ $msg->subject }}</h3>
                        <p class="mt-1.5 line-clamp-2 text-sm text-[#5A6B61]">
                            <span class="font-semibold text-[#1E2B24]">Pertanyaan:</span> {{ $msg->message }}
                        </p>

                        @if ($msg->isReplied())
                            <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                                <div class="flex items-center gap-2">
                                    <span class="grid h-6 w-6 place-items-center rounded-full bg-[#4F7A63] text-xs font-bold text-white shadow-sm">
                                        ⚡
                                    </span>
                                    <p class="text-xs font-bold text-[#1E2B24]">
                                        Jawaban Resmi Admin GoSport
                                        <span class="font-normal text-[#5A6B61]">({{ $msg->replied_at?->diffForHumans() }})</span>:
                                    </p>
                                </div>
                                <p class="mt-2 line-clamp-2 text-sm text-[#1E2B24] whitespace-pre-line">
                                    {{ $msg->admin_reply }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 flex items-center justify-end border-t border-[#D5E0D8] pt-4">
                        <a href="{{ route('account.messages.show', $msg) }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-[#D5E0D8] bg-white px-5 py-2.5 text-xs font-bold text-[#4F7A63] shadow-sm transition hover:bg-[#F6F9F7]">
                            <x-icon name="eye" class="h-4 w-4" /> Buka Percakapan Lengkap
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-[#D5E0D8] bg-white p-12 text-center shadow-sm">
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#F6F9F7] text-[#5A6B61]">
                        <x-icon name="mail" class="h-8 w-8 text-[#4F7A63]" />
                    </div>
                    <h3 class="mt-4 text-lg font-extrabold text-[#1E2B24]">Belum Ada Pesan Masuk</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm text-[#5A6B61]">
                        Jika Anda memiliki pertanyaan seputar stok produk, rekomendasi ukuran, kendala pembayaran, atau pengiriman, silakan kirimkan pesan kepada kami.
                    </p>
                    <a href="{{ route('pages.contact') }}"
                        class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#2F4F3E] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#243F31]">
                        <x-icon name="plus" class="h-4 w-4" /> Hubungi Kami Sekarang
                    </a>
                </div>
            @endforelse

            @if ($messages->hasPages())
                <div class="pt-2">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
