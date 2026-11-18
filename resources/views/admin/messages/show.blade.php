@extends('layouts.admin')

@section('title', 'Detail Pesan Kontak - GoSport Admin')
@section('admin-title', 'Detail Pesan Masuk')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800">
                <x-icon name="check-circle" class="h-5 w-5 shrink-0 text-emerald-600" />
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.messages.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-[#D5E0D8] bg-white px-4 py-2.5 text-xs font-bold text-[#5A6B61] shadow-sm transition hover:bg-[#F6F9F7] hover:text-[#1E2B24]">
                <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke Daftar Pesan
            </a>

            <form action="{{ route('admin.messages.destroy', $contactMessage) }}" method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-4 py-2.5 text-xs font-bold text-red-600 shadow-sm transition hover:bg-red-50">
                    <x-icon name="trash" class="h-4 w-4" /> Hapus Pesan
                </button>
            </form>
        </div>

        {{-- Kartu Pesan Dari Customer --}}
        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-[#D5E0D8] pb-6">
                <div class="flex items-center gap-4">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-[#4F7A63]/10 text-lg font-extrabold text-[#4F7A63]">
                        {{ strtoupper(substr($contactMessage->name, 0, 1)) }}
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-extrabold text-[#1E2B24]">{{ $contactMessage->name }}</h2>
                            @if ($contactMessage->user_id)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                                    Customer Terdaftar
                                </span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-[11px] font-medium text-gray-600">
                                    Tamu
                                </span>
                            @endif
                        </div>
                        <a href="mailto:{{ $contactMessage->email }}?subject=Re:%20{{ urlencode($contactMessage->subject) }}"
                            class="inline-flex items-center gap-1 text-sm text-[#4F7A63] hover:underline">
                            <x-icon name="mail" class="h-3.5 w-3.5" /> {{ $contactMessage->email }}
                        </a>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-xs text-[#5A6B61]">Diterima pada</p>
                    <p class="text-sm font-semibold text-[#1E2B24]">{{ $contactMessage->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                    @if ($contactMessage->isReplied())
                        <span class="mt-1 inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                            ✓ Sudah Dibalas
                        </span>
                    @else
                        <span class="mt-1 inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                            Menunggu Balasan
                        </span>
                    @endif
                </div>
            </div>

            <div class="pt-6">
                <p class="text-xs font-bold uppercase tracking-wider text-[#5A6B61]">Subjek</p>
                <h3 class="mt-1 text-xl font-extrabold text-[#1E2B24]">{{ $contactMessage->subject }}</h3>

                <p class="mt-6 text-xs font-bold uppercase tracking-wider text-[#5A6B61]">Isi Pesan Customer</p>
                <div class="mt-2 rounded-2xl border border-[#D5E0D8] bg-[#F6F9F7] p-5 text-sm leading-relaxed text-[#1E2B24] whitespace-pre-line">
{{ $contactMessage->message }}
                </div>
            </div>
        </div>

        {{-- Bagian Balasan Admin --}}
        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm sm:p-8">
            @if ($contactMessage->isReplied())
                <div class="mb-6 flex items-center justify-between border-b border-[#D5E0D8] pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                ✓ Balasan Terkirim
                            </span>
                            @if ($contactMessage->customer_read_at)
                                <span class="text-xs text-emerald-700">
                                    (Sudah dilihat customer pada {{ $contactMessage->customer_read_at->translatedFormat('d M Y, H:i') }})
                                </span>
                            @else
                                <span class="text-xs text-[#5A6B61]">
                                    (Belum dibuka customer)
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-[#5A6B61]">
                            Dibalas oleh <span class="font-bold text-[#1E2B24]">{{ $contactMessage->repliedByUser?->name ?? 'Admin' }}</span>
                            pada {{ $contactMessage->replied_at?->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#4F7A63]/30 bg-[#4F7A63]/5 p-5 text-sm leading-relaxed text-[#1E2B24] whitespace-pre-line">
{{ $contactMessage->admin_reply }}
                </div>

                <div class="mt-6 border-t border-[#D5E0D8] pt-6">
                    <p class="text-sm font-bold text-[#1E2B24]">Perbarui / Kirim Jawaban Tambahan</p>
                    <p class="text-xs text-[#5A6B61]">Customer akan langsung melihat perubahan jawaban ini di halaman akun mereka.</p>
                </div>
            @else
                <div>
                    <h3 class="text-lg font-extrabold text-[#1E2B24]">Balas Pesan Customer</h3>
                    <p class="text-xs text-[#5A6B61]">Tulis jawaban resmi di bawah ini. Balasan akan langsung tampil di menu Akun > Pesan & Bantuan milik customer.</p>
                </div>
            @endif

            <form action="{{ route('admin.messages.reply', $contactMessage) }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <textarea name="reply" rows="5" required placeholder="Tulis balasan Anda untuk customer di sini..."
                        class="w-full rounded-2xl border @error('reply') border-red-400 bg-red-50 @else border-[#D5E0D8] bg-[#F6F9F7] @enderror p-4 text-sm text-[#1E2B24] outline-none transition focus:border-[#4F7A63] focus:bg-white">{{ old('reply', $contactMessage->admin_reply) }}</textarea>
                    @error('reply')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#2F4F3E] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#243F31]">
                        <x-icon name="check" class="h-4 w-4" />
                        {{ $contactMessage->isReplied() ? 'Simpan & Perbarui Balasan' : 'Kirim Balasan ke Customer' }}
                    </button>

                    <a href="mailto:{{ $contactMessage->email }}?subject=Re:%20{{ urlencode($contactMessage->subject) }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5A6B61] hover:text-[#4F7A63]">
                        <x-icon name="mail" class="h-4 w-4" /> Atau balas lewat aplikasi email eksternal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
