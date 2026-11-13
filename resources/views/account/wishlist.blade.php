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
        <div class="rounded-3xl border border-[#8CC4FF] bg-white py-20 text-center shadow-sm">
            <x-icon name="heart" class="mx-auto h-14 w-14 text-[#8CC4FF]" />
            <h2 class="mt-4 text-xl font-bold text-[#0A2A5E]">Wishlist kamu masih kosong</h2>
            <p class="mt-1 text-sm text-neutral-500">Simpan produk favoritmu agar mudah ditemukan nanti.</p>
            <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-xl bg-[#FF7A00] px-7 py-3.5 text-sm font-bold text-[#10213F] shadow-sm transition hover:brightness-105">
                Jelajahi Produk
            </a>
        </div>
    @endif
@endsection
