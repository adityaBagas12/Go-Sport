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
        <div class="rounded-3xl border border-dashed border-neutral-200 bg-white py-20 text-center">
            <x-icon name="heart" class="mx-auto h-14 w-14 text-neutral-300" />
            <h2 class="mt-4 text-xl font-bold text-neutral-900">Wishlist kamu masih kosong</h2>
            <p class="mt-1 text-sm text-neutral-500">Simpan produk favoritmu agar mudah ditemukan nanti.</p>
            <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-xl bg-brand-500 px-7 py-3.5 text-sm font-bold text-white transition hover:bg-brand-600">
                Jelajahi Produk
            </a>
        </div>
    @endif
@endsection
