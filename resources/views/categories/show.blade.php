@extends('layouts.app')

@section('title', $category->name . ' - GoSport')

@section('content')
    <div class="border-b border-neutral-200 bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <a href="{{ route('categories.index') }}" class="hover:text-brand-600">Kategori</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="font-medium text-neutral-900">{{ $category->name }}</span>
            </nav>
            <div class="mt-4 flex items-center gap-4">
                <span class="grid h-16 w-16 place-items-center rounded-2xl bg-brand-50 text-3xl">{{ $category->icon }}</span>
                <div>
                    <h1 class="text-3xl font-extrabold text-neutral-900">{{ $category->name }}</h1>
                    <p class="mt-1 text-neutral-500">{{ $category->description }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-page py-10">
        <p class="mb-6 text-sm text-neutral-500">{{ $products->total() }} produk tersedia</p>

        @if ($products->count())
            <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-10">{{ $products->links() }}</div>
        @else
            <div class="rounded-3xl border border-dashed border-neutral-200 bg-white py-20 text-center">
                <x-icon name="box" class="mx-auto h-12 w-12 text-neutral-300" />
                <h3 class="mt-4 text-lg font-bold text-neutral-900">Belum ada produk</h3>
                <p class="mt-1 text-sm text-neutral-500">Produk untuk kategori ini belum tersedia.</p>
            </div>
        @endif
    </div>
@endsection
