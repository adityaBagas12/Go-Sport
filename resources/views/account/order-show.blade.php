@extends('layouts.account')

@section('account-title', 'Detail Pesanan')
@section('title', 'Detail Pesanan - GoSport')

@section('account-content')
    @php
        $steps = ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai'];
        $currentIndex = array_search($order->status, $steps, true);
        $isCancelled = $order->status === 'dibatalkan';
    @endphp

    <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-[#5A6B61]">Nomor Pesanan</p>
                <p class="text-xl font-extrabold text-[#1E2B24]">{{ $order->order_number }}</p>
                <p class="mt-1 text-sm text-[#5A6B61]">Dibuat {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
            <div class="text-right">
                <span class="inline-flex rounded-full px-4 py-1.5 text-xs font-bold {{ $isCancelled ? 'bg-red-100 text-[#E53935]' : 'border border-[#4F7A63]/30 bg-[#4F7A63]/10 text-[#4F7A63]' }}">
                    {{ \App\Models\Order::statusLabel($order->status) }}
                </span>
                <p class="mt-2 text-xs text-[#5A6B61]">Pembayaran: <span class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">{{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}</span></p>
            </div>
        </div>

        @unless ($isCancelled)
            <div class="mt-8">
                <div class="flex items-center">
                    @foreach ($steps as $index => $step)
                        <div class="flex flex-1 items-center {{ $loop->last ? 'flex-none' : '' }}">
                            <div class="flex flex-col items-center">
                                <span class="grid h-10 w-10 place-items-center rounded-full text-sm font-bold {{ $index <= $currentIndex ? 'bg-[#4F7A63] text-white shadow-sm' : 'bg-neutral-100 text-neutral-400' }}">
                                    @if ($index < $currentIndex)
                                        <x-icon name="check" class="h-5 w-5" />
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </span>
                                <span class="mt-2 w-24 text-center text-xs font-semibold {{ $index <= $currentIndex ? 'text-[#1E2B24]' : 'text-[#5A6B61]' }}">
                                    {{ \App\Models\Order::statusLabel($step) }}
                                </span>
                            </div>
                            @unless ($loop->last)
                                <div class="mx-2 mb-6 h-1 flex-1 rounded-full {{ $index < $currentIndex ? 'bg-[#4F7A63]' : 'bg-neutral-200' }}"></div>
                            @endunless
                        </div>
                    @endforeach
                </div>
            </div>
        @endunless

        @if ($order->status === 'menunggu_pembayaran')
            <div class="mt-6 flex flex-wrap gap-3 rounded-2xl border border-[#D5E0D8] bg-[#F6F9F7] p-4">
                @if ($order->payment)
                    <p class="flex-1 text-sm text-[#1E2B24]">
                        Selesaikan pembayaran melalui invoice
                        <span class="font-bold">#{{ $order->payment->invoice_number }}</span>
                        ({{ $order->payment->methodLabel() }}) agar pesanan dapat diproses.
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('payment.invoice', $order->payment->invoice_number) }}"
                            class="inline-flex items-center rounded-xl bg-[#4F7A63] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                            Lihat Invoice &amp; Bayar
                        </a>
                        <form action="{{ route('order.cancel', $order->order_number) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-xl border border-[#E53935]/40 px-4 py-2.5 text-sm font-bold text-[#E53935] hover:bg-[#E53935]/10">Batalkan</button>
                        </form>
                    </div>
                @else
                    <p class="flex-1 text-sm text-amber-800">
                        Invoice pembayaran untuk pesanan ini belum tersedia.
                        Hubungi tim GoSport untuk assistance.
                    </p>
                    <form action="{{ route('order.cancel', $order->order_number) }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-xl border border-[#E53935]/40 px-4 py-2.5 text-sm font-bold text-[#E53935] hover:bg-[#E53935]/10">Batalkan</button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 text-lg font-extrabold text-[#1E2B24]">
                <x-icon name="map-pin" class="h-5 w-5 text-[#4F7A63]" /> Alamat Pengiriman
            </h2>
            <div class="mt-4 text-sm text-[#1E2B24]">
                <p class="font-bold text-[#1E2B24]">{{ $order->recipient_name }}</p>
                <p>{{ $order->recipient_phone }}</p>
                <p class="mt-1 text-[#5A6B61]">{{ $order->full_address }}, {{ $order->district }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                @if ($order->note)
                    <p class="mt-3 rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-2 text-xs text-[#5A6B61]">Catatan: {{ $order->note }}</p>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <h2 class="flex items-center gap-2 text-lg font-extrabold text-[#1E2B24]">
                <x-icon name="truck" class="h-5 w-5 text-[#4F7A63]" /> Pengiriman & Pembayaran
            </h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[#5A6B61]">Jasa Kirim</dt>
                    <dd class="font-semibold text-[#1E2B24]">{{ $order->courier }} - {{ $order->shipping_service }}</dd>
                </div>
                @if ($order->payment)
                    <div class="flex justify-between">
                        <dt class="text-[#5A6B61]">Nomor Invoice</dt>
                        <dd class="font-mono font-semibold text-[#1E2B24]">#{{ $order->payment->invoice_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-[#5A6B61]">Metode Bayar</dt>
                        <dd class="font-semibold text-[#1E2B24]">{{ $order->payment->methodLabel() }}</dd>
                    </div>
                    @if ($order->payment->payment_type)
                        <div class="flex justify-between">
                            <dt class="text-[#5A6B61]">Metode Terpilih</dt>
                            <dd class="font-semibold text-[#1E2B24]">{{ $order->payment->paymentTypeLabel() }}</dd>
                        </div>
                    @endif
                    @if ($order->payment->hasAccountNumber())
                        <div class="flex justify-between gap-4">
                            <dt class="shrink-0 text-[#5A6B61]">Nomor Virtual Account</dt>
                            <dd class="select-all break-all text-right font-mono font-bold text-[#4F7A63]">
                                {{ $order->payment->bank_name ? strtoupper($order->payment->bank_name).' ' : '' }}{{ $order->payment->account_number }}
                            </dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-[#5A6B61]">Status Invoice</dt>
                        <dd class="font-semibold text-[#1E2B24]">{{ \App\Models\Payment::statusLabel($order->payment->payment_status) }}</dd>
                    </div>
                @else
                    <div class="flex justify-between">
                        <dt class="text-[#5A6B61]">Metode Bayar</dt>
                        <dd class="font-semibold text-[#1E2B24]">{{ config('gosport.payment_methods')[$order->payment_method] ?? $order->payment_method }}</dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-[#5A6B61]">Status Bayar</dt>
                    <dd class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">{{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-[#1E2B24]">Rincian Produk</h2>

        <div class="mt-4 divide-y divide-[#D5E0D8]">
            @foreach ($order->items as $item)
                <div class="flex gap-4 py-4">
                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="h-20 w-20 shrink-0 rounded-xl object-cover">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-[#1E2B24]">{{ $item->product_name }}</p>
                        <p class="mt-0.5 text-xs text-[#5A6B61]">
                            {{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}
                            @if ($item->size) &middot; Ukuran {{ $item->size }} @endif
                            @if ($item->color) &middot; {{ $item->color }} @endif
                        </p>

                        @php
                            $itemReview = \App\Models\Review::where('user_id', auth()->id())
                                ->where('order_id', $order->id)
                                ->where('product_id', $item->product_id)
                                ->first();
                        @endphp

                        @if ($order->payment_status === 'paid' || in_array($order->status, ['diproses', 'dikirim', 'selesai'], true))
                            @if ($itemReview)
                                <div class="mt-2 flex flex-wrap items-center gap-2 rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-2 text-xs">
                                    <span class="font-bold text-[#1E2B24]">Ulasan Anda:</span>
                                    <span class="flex items-center text-amber-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-icon :name="$i <= $itemReview->rating ? 'star' : 'star-outline'" class="h-3.5 w-3.5" />
                                        @endfor
                                    </span>
                                    <span class="font-bold text-[#1E2B24]">{{ $itemReview->rating }}/5</span>
                                    @if ($itemReview->comment)
                                        <span class="text-[#5A6B61]">&mdash; "{{ $itemReview->comment }}"</span>
                                    @endif
                                </div>
                            @else
                                <div class="mt-3">
                                    <details class="group rounded-2xl border border-[#D5E0D8] bg-[#F6F9F7] p-3 text-xs">
                                        <summary class="flex cursor-pointer list-none items-center justify-between font-bold text-[#4F7A63] hover:underline">
                                            <span class="flex items-center gap-1.5">
                                                <x-icon name="star" class="h-4 w-4 text-amber-500" /> Beri Penilaian &amp; Ulasan Bintang
                                            </span>
                                            <x-icon name="chevron-down" class="h-4 w-4 text-[#5A6B61] transition group-open:rotate-180" />
                                        </summary>
                                        <form action="{{ route('reviews.store') }}" method="POST" class="mt-3 space-y-3 border-t border-[#D5E0D8] pt-3">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                                            <div>
                                                <label class="block font-bold text-[#1E2B24] mb-1">Pilih Rating Bintang:</label>
                                                <div class="flex items-center gap-2">
                                                    @for ($r = 5; $r >= 1; $r--)
                                                        <label class="flex items-center gap-1 cursor-pointer rounded-lg border border-[#D5E0D8] bg-white px-2.5 py-1 text-xs font-semibold hover:border-[#4F7A63]">
                                                            <input type="radio" name="rating" value="{{ $r }}" {{ $r === 5 ? 'checked' : '' }} class="text-[#4F7A63] focus:ring-[#4F7A63]">
                                                            <span class="text-amber-500">★</span> {{ $r }}
                                                        </label>
                                                    @endfor
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block font-bold text-[#1E2B24] mb-1">Ulasan / Komentar (Opsional):</label>
                                                <textarea name="comment" rows="2" placeholder="Bagikan kepuasan Anda mengenai produk ini..."
                                                    class="w-full rounded-xl border border-[#D5E0D8] p-2.5 text-xs text-[#1E2B24] outline-none focus:border-[#4F7A63] bg-white"></textarea>
                                            </div>

                                            <button type="submit" class="rounded-xl bg-[#4F7A63] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#3E6350]">
                                                Kirim Penilaian
                                            </button>
                                        </form>
                                    </details>
                                </div>
                            @endif
                        @endif
                    </div>
                    <p class="text-sm font-extrabold text-[#1E2B24]">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        <dl class="mt-4 space-y-3 border-t border-[#D5E0D8] pt-4 text-sm">
            <div class="flex justify-between">
                <dt class="text-[#5A6B61]">Subtotal</dt>
                <dd class="font-semibold text-[#1E2B24]">{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-[#5A6B61]">Biaya Pengiriman</dt>
                <dd class="font-semibold text-[#1E2B24]">{{ 'Rp ' . number_format($order->shipping_fee, 0, ',', '.') }}</dd>
            </div>
            @if ($order->discount > 0)
                <div class="flex justify-between text-[#4F7A63]">
                    <dt>Diskon</dt>
                    <dd class="font-semibold">- {{ 'Rp ' . number_format($order->discount, 0, ',', '.') }}</dd>
                </div>
            @endif
            <div class="flex justify-between border-t border-[#D5E0D8] pt-3 text-base">
                <dt class="font-bold text-[#1E2B24]">Total Pembayaran</dt>
                <dd class="font-black text-[#4F7A63]">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</dd>
            </div>
        </dl>
    </div>
@endsection
