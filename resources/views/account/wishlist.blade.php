@extends('layouts.account')

@section('account-title', 'Wishlist')
@section('title', 'Wishlist - GoSport')

@section('account-content')
    @if ($items->count())
        <div class="grid grid-cols-2 gap-4 sm:gap-6 xl:grid-cols-3">
            @foreach ($items as $item)
                <x-product-card :product="$item->product" />
            @endforeach
        </div>
    @else
        <div class="rounded-3xl border border-[#D5E0D8] bg-white py-20 text-center shadow-sm">
            <x-icon name="heart" class="mx-auto h-14 w-14 text-[#5A6B61]" />
            <h2 class="mt-4 text-xl font-bold text-[#1E2B24]">Wishlist kamu masih kosong</h2>
            <p class="mt-1 text-sm text-[#5A6B61]">Simpan produk favoritmu agar mudah ditemukan nanti.</p>
            <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-xl bg-[#4F7A63] px-7 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                Jelajahi Produk
            </a>
        </div>
    @endif
@endsection
