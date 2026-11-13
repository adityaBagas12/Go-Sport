@extends('layouts.app')

@section('title', 'Promo & Diskon - GoSport')

@section('content')
    <section class="border-b border-[#8CC4FF]/30 bg-[#0A2A5E] text-white">
        <div class="container-page py-14 text-center">
            <p class="inline-flex items-center gap-2 rounded-full border border-[#8CC4FF]/30 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-[#FF7A00]">
                <x-icon name="sparkles" class="h-4 w-4" /> Promo Spesial
            </p>
            <h1 class="mt-4 text-3xl font-black text-white sm:text-4xl">Diskon Hingga 40%</h1>
            <p class="mx-auto mt-3 max-w-xl text-blue-100/90">
                Gunakan voucher <span class="font-bold text-[#FF7A00]">GOSPORT10</span> (diskon 10%) atau <span class="font-bold text-[#FF7A00]">GOSPORT50</span>
                (potongan Rp 50.000) untuk belanjaan favoritmu.
            </p>
        </div>
    </section>

    <div class="container-page py-12">
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ([['GOSPORT10', 'Diskon 10%', 'Min. belanja Rp 100.000'], ['GOSPORT50', 'Potongan Rp 50.000', 'Min. belanja Rp 300.000']] as [$code, $title, $desc])
                <div class="flex items-center justify-between gap-4 rounded-3xl border-2 border-dashed border-[#8CC4FF] bg-[#E6F1FF] p-6 shadow-sm">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-[#0A6CFF]">Kode Voucher</p>
                        <p class="mt-1 text-2xl font-black tracking-wider text-[#0A2A5E]">{{ $code }}</p>
                        <p class="mt-1 text-sm font-semibold text-[#10213F]">{{ $title }}</p>
                        <p class="text-xs text-neutral-500">{{ $desc }}</p>
                    </div>
                    <button type="button" data-copy="{{ $code }}"
                        class="shrink-0 rounded-xl bg-[#0A2A5E] px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#0A6CFF]">
                        Salin
                    </button>
                </div>
            @endforeach
        </div>

        <h2 class="mt-14 text-2xl font-extrabold text-[#0A2A5E]">Produk Sedang Promo</h2>
        <p class="mt-1 text-neutral-500">{{ $products->total() }} produk dengan harga spesial</p>

        @if ($products->count())
            <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        @else
            <div class="mt-8 rounded-3xl border border-[#8CC4FF] bg-white py-20 text-center shadow-sm">
                <h3 class="text-lg font-bold text-[#0A2A5E]">Belum ada produk promo</h3>
            </div>
        @endif
    </div>
@endsection
