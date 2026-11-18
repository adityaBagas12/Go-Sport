@extends('layouts.admin')

@section('title', 'Pesan Kontak - GoSport Admin')
@section('admin-title', 'Pesan Masuk Kontak')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800">
                <x-icon name="check-circle" class="h-5 w-5 shrink-0 text-emerald-600" />
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            {{-- Tabs & Search --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.messages.index', ['status' => 'all', 'search' => request('search')]) }}"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition {{ $status === 'all' ? 'bg-[#2F4F3E] text-white shadow-sm' : 'border border-[#D5E0D8] bg-[#F6F9F7] text-[#5A6B61] hover:text-[#1E2B24]' }}">
                        Semua ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.messages.index', ['status' => 'unread', 'search' => request('search')]) }}"
                        class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold transition {{ $status === 'unread' ? 'bg-[#2F4F3E] text-white shadow-sm' : 'border border-[#D5E0D8] bg-[#F6F9F7] text-[#5A6B61] hover:text-[#1E2B24]' }}">
                        Belum Dibaca
                        @if ($unreadCount > 0)
                            <span class="rounded-full bg-[#E53935] px-1.5 py-0.2 text-[10px] text-white">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.messages.index', ['status' => 'read', 'search' => request('search')]) }}"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition {{ $status === 'read' ? 'bg-[#2F4F3E] text-white shadow-sm' : 'border border-[#D5E0D8] bg-[#F6F9F7] text-[#5A6B61] hover:text-[#1E2B24]' }}">
                        Sudah Dibaca
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.messages.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="relative flex items-center">
                        <x-icon name="search" class="pointer-events-none absolute left-3.5 h-4 w-4 text-[#5A6B61]" />
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, subjek..."
                            class="w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] py-2 pl-9 pr-3 text-sm text-[#1E2B24] outline-none transition focus:border-[#4F7A63] focus:bg-white sm:w-64">
                    </div>
                    <button type="submit" class="rounded-xl bg-[#2F4F3E] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#243F31]">
                        Cari
                    </button>
                    @if (request('search'))
                        <a href="{{ route('admin.messages.index', ['status' => $status]) }}" class="text-xs text-[#5A6B61] underline hover:text-[#1E2B24]">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="mt-6 overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                        <tr class="border-b border-[#D5E0D8] text-left text-xs uppercase tracking-wide text-[#5A6B61]">
                            <th class="pb-3 pr-4 font-semibold">Status</th>
                            <th class="pb-3 pr-4 font-semibold">Pengirim</th>
                            <th class="pb-3 pr-4 font-semibold">Subjek & Pesan</th>
                            <th class="pb-3 pr-4 font-semibold">Tanggal</th>
                            <th class="pb-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#D5E0D8]">
                        @forelse ($messages as $msg)
                            <tr class="transition hover:bg-[#F6F9F7] {{ ! $msg->is_read ? 'bg-emerald-50/40 font-medium' : '' }}">
                                <td class="py-3.5 pr-4">
                                    <div class="flex flex-col items-start gap-1">
                                        @if (! $msg->is_read)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#E53935]/10 px-2 py-0.5 text-[11px] font-bold text-[#E53935]">
                                                <span class="h-1.5 w-1.5 rounded-full bg-[#E53935]"></span> Baru
                                            </span>
                                        @endif
                                        @if ($msg->isReplied())
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800">
                                                ✓ Dibalas
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-800">
                                                Menunggu Balasan
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 pr-4">
                                    <p class="font-bold text-[#1E2B24]">{{ $msg->name }}</p>
                                    <p class="text-xs text-[#5A6B61]">{{ $msg->email }}</p>
                                </td>
                                <td class="max-w-md py-3.5 pr-4">
                                    <p class="truncate font-semibold text-[#1E2B24]">{{ $msg->subject }}</p>
                                    <p class="truncate text-xs text-[#5A6B61]">{{ Str::limit($msg->message, 80) }}</p>
                                </td>
                                <td class="py-3.5 pr-4 text-xs text-[#5A6B61]">
                                    <p>{{ $msg->created_at->translatedFormat('d M Y') }}</p>
                                    <p class="text-[11px] text-[#5A6B61]/70">{{ $msg->created_at->format('H:i') }} WIB</p>
                                </td>
                                <td class="py-3.5 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.messages.show', $msg) }}"
                                            class="inline-flex items-center gap-1 rounded-lg border border-[#D5E0D8] bg-white px-3 py-1.5 text-xs font-bold text-[#4F7A63] shadow-sm transition hover:bg-[#F6F9F7]">
                                            <x-icon name="eye" class="h-3.5 w-3.5" /> Baca
                                        </a>
                                        <form action="{{ route('admin.messages.destroy', $msg) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center rounded-lg border border-red-200 bg-white p-1.5 text-xs text-red-600 shadow-sm transition hover:bg-red-50"
                                                title="Hapus Pesan">
                                                <x-icon name="trash" class="h-3.5 w-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-[#5A6B61]">
                                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#F6F9F7] text-[#5A6B61]">
                                        <x-icon name="mail" class="h-6 w-6" />
                                    </div>
                                    <p class="mt-3 text-sm font-semibold">Tidak ada pesan ditemukan.</p>
                                    <p class="text-xs text-[#5A6B61]/80">Pesan dari pengunjung toko melalui halaman kontak akan tampil di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($messages->hasPages())
                <div class="mt-6 border-t border-[#D5E0D8] pt-4">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
