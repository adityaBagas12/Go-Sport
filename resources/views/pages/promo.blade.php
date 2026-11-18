@extends('layouts.app')

@section('title', 'Promo & Diskon - GoSport')

@section('content')
    <section class="border-b border-[#243F31] bg-[#2F4F3E] text-white">
        <div class="container-page py-14 text-center">
            <p class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-[#DCE8DF]">
                <x-icon name="sparkles" class="h-4 w-4" /> Promo Spesial
            </p>
            <h1 class="mt-4 text-3xl font-black text-white !text-white sm:text-4xl" style="color: #ffffff !important;">
                @if ($maxDiscount)
                    Diskon Hingga {{ $maxDiscount }}%
                @else
                    Promo &amp; Diskon Spesial
                @endif
            </h1>
            <p class="mx-auto mt-3 max-w-xl text-emerald-100/80">
                @if ($coupons->isNotEmpty())
                    Gunakan voucher
                    @foreach ($coupons->take(3) as $c)
                        <span class="font-bold text-[#DCE8DF]">{{ $c->code }}</span>{{ ! $loop->last ? ($loop->remaining == 1 ? ' atau ' : ', ') : '' }}
                    @endforeach
                    untuk belanjaan favoritmu.
                @else
                    Temukan berbagai promo dan harga spesial untuk perlengkapan olahraga terbaik di GoSport.
                @endif
            </p>
        </div>
    </section>

    <div class="container-page py-12">
        @if ($coupons->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($coupons as $coupon)
                    @php
                        $title = $coupon->type === 'percent'
                            ? "Diskon {$coupon->value}%"
                            : 'Potongan Rp ' . number_format($coupon->value, 0, ',', '.');
                        $desc = $coupon->min_order > 0
                            ? 'Min. belanja Rp ' . number_format($coupon->min_order, 0, ',', '.')
                            : 'Tanpa minimal belanja';
                    @endphp
                    <div class="flex items-center justify-between gap-4 rounded-3xl border-2 border-dashed border-[#D5E0D8] bg-[#DCE8DF]/40 p-6 shadow-sm transition hover:border-[#4F7A63]">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold uppercase tracking-wider text-[#4F7A63]">Kode Voucher</p>
                            <p class="mt-1 truncate text-2xl font-black tracking-wider text-[#1E2B24]">{{ $coupon->code }}</p>
                            <p class="mt-1 text-sm font-semibold text-[#1E2B24]">{{ $title }}</p>
                            <p class="text-xs text-[#5A6B61]">{{ $desc }}</p>
                        </div>
                        <button type="button" data-copy="{{ $coupon->code }}"
                            class="shrink-0 rounded-xl bg-[#2F4F3E] px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#4F7A63]">
                            Salin
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-3xl border border-[#D5E0D8] bg-white p-8 text-center shadow-sm">
                <p class="text-sm font-semibold text-[#5A6B61]">Saat ini belum ada kode voucher aktif. Nantikan promo menarik berikutnya!</p>
            </div>
        @endif

        <h2 class="mt-14 text-2xl font-extrabold text-[#1E2B24]">Produk Sedang Promo</h2>
        <p class="mt-1 text-[#5A6B61]">{{ $products->total() }} produk dengan harga spesial</p>

        @if ($products->count())
            <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        @else
            <div class="mt-8 rounded-3xl border border-[#D5E0D8] bg-white py-20 text-center shadow-sm">
                <h3 class="text-lg font-bold text-[#1E2B24]">Belum ada produk promo</h3>
            </div>
        @endif
    </div>
@endsection
