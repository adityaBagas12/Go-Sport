@extends('layouts.app')

@section('title', $category->name . ' - GoSport')

@section('content')
    <div class="border-b border-[#8CC4FF]/30 bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-[#0A6CFF]">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <a href="{{ route('categories.index') }}" class="hover:text-[#0A6CFF]">Kategori</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="font-medium text-[#10213F]">{{ $category->name }}</span>
            </nav>
            <div class="mt-4 flex items-center gap-4">
                <span class="grid h-16 w-16 place-items-center rounded-2xl border border-[#8CC4FF]/50 bg-[#E6F1FF] text-3xl">{{ $category->icon }}</span>
                <div>
                    <h1 class="text-3xl font-extrabold text-[#0A2A5E]">{{ $category->name }}</h1>
                    <p class="mt-1 text-neutral-600">{{ $category->description }}</p>
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
            <div class="rounded-3xl border border-[#8CC4FF] bg-white py-20 text-center shadow-sm">
                <x-icon name="box" class="mx-auto h-12 w-12 text-[#8CC4FF]" />
                <h3 class="mt-4 text-lg font-bold text-[#0A2A5E]">Belum ada produk</h3>
                <p class="mt-1 text-sm text-neutral-500">Produk untuk kategori ini belum tersedia.</p>
            </div>
        @endif
    </div>
@endsection
