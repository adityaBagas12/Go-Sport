@extends('layouts.app')

@section('title', 'Promo & Diskon - GoSport')

@section('content')
    <section class="bg-gradient-to-r from-brand-600 to-amber-400 text-white">
        <div class="container-page py-14 text-center">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-1.5 text-xs font-bold uppercase tracking-wider">
                <x-icon name="sparkles" class="h-4 w-4" /> Promo Spesial
            </p>
            <h1 class="mt-4 text-3xl font-black sm:text-4xl">Diskon Hingga 40%</h1>
            <p class="mx-auto mt-3 max-w-xl text-white/90">
                Gunakan voucher <span class="font-bold">GOSPORT10</span> (diskon 10%) atau <span class="font-bold">GOSPORT50</span>
                (potongan Rp 50.000) untuk belanjaan favoritmu.
            </p>
        </div>
    </section>

    <div class="container-page py-12">
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ([['GOSPORT10', 'Diskon 10%', 'Min. belanja Rp 100.000'], ['GOSPORT50', 'Potongan Rp 50.000', 'Min. belanja Rp 300.000']] as [$code, $title, $desc])
                <div class="flex items-center justify-between gap-4 rounded-3xl border-2 border-dashed border-brand-300 bg-brand-50 p-6">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-600">Kode Voucher</p>
                        <p class="mt-1 text-2xl font-black tracking-wider text-neutral-900">{{ $code }}</p>
                        <p class="mt-1 text-sm font-semibold text-neutral-700">{{ $title }}</p>
                        <p class="text-xs text-neutral-500">{{ $desc }}</p>
                    </div>
                    <button type="button" data-copy="{{ $code }}"
                        class="shrink-0 rounded-xl bg-neutral-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-brand-500">
                        Salin
                    </button>
                </div>
            @endforeach
        </div>

        <h2 class="mt-14 text-2xl font-extrabold text-neutral-900">Produk Sedang Promo</h2>
        <p class="mt-1 text-neutral-500">{{ $products->total() }} produk dengan harga spesial</p>

        @if ($products->count())
            <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        @else
            <div class="mt-8 rounded-3xl border border-dashed border-neutral-200 bg-white py-20 text-center">
                <h3 class="text-lg font-bold text-neutral-900">Belum ada produk promo</h3>
            </div>
        @endif
    </div>
@endsection
