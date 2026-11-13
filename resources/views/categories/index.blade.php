@extends('layouts.app')

@section('title', 'Kategori Produk - GoSport')

@section('content')
    <div class="border-b border-neutral-200 bg-white">
        <div class="container-page py-10 text-center">
            <p class="text-sm font-bold uppercase tracking-wider text-brand-500">Kategori</p>
            <h1 class="mt-2 text-3xl font-extrabold text-neutral-900 sm:text-4xl">Semua Kategori Olahraga</h1>
            <p class="mx-auto mt-3 max-w-xl text-neutral-500">
                Temukan perlengkapan dari cabang olahraga favoritmu, mulai dari sepatu, jersey, bola, hingga alat fitness.
            </p>
        </div>
    </div>

    <div class="container-page py-12">
        <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}"
                    class="group overflow-hidden rounded-3xl border border-neutral-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="aspect-[4/3] overflow-hidden bg-neutral-100">
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" loading="lazy"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ $category->icon }}</span>
                            <h2 class="text-base font-bold text-neutral-900 group-hover:text-brand-600">{{ $category->name }}</h2>
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm text-neutral-500">{{ $category->description }}</p>
                        <p class="mt-3 text-xs font-semibold text-brand-600">{{ $category->products_count }} produk &rarr;</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
