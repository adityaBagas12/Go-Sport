@extends('layouts.app')

@section('title', $product->name . ' - GoSport')
@section('meta_description', Str::limit(strip_tags($product->description), 150))

@section('content')
    @php
        $gallery = collect([$product->image])->merge($product->images->pluck('image'))->filter()->unique()->values();
        $wishlisted = $product->isWishlisted();
        $ratingCount = $product->reviews->count();
    @endphp

    <div class="border-b border-neutral-200 bg-white">
        <div class="container-page py-5">
            <nav class="flex flex-wrap items-center gap-2 text-sm text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <a href="{{ route('products.index') }}" class="hover:text-brand-600">Produk</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-brand-600">{{ $product->category->name }}</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="truncate font-medium text-neutral-900">{{ Str::limit($product->name, 40) }}</span>
            </nav>
        </div>
    </div>

    <div class="container-page py-10">
        <div class="grid gap-10 lg:grid-cols-2">
            <div data-gallery>
                <div class="overflow-hidden rounded-3xl border border-neutral-100 bg-white shadow-sm">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}"
                        class="aspect-square w-full object-cover">
                </div>
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($product->badge)
                        <span class="rounded-full bg-neutral-900 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">
                            {{ ['baru' => 'Baru', 'terlaris' => 'Terlaris', 'promo' => 'Promo'][$product->badge] ?? $product->badge }}
                        </span>
                    @endif
                    <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-600">{{ $product->category->name }}</span>
                    @if ($product->is_discounted)
                        <span class="rounded-full bg-red-500 px-3 py-1 text-xs font-bold text-white">Diskon {{ $product->discount_percent }}%</span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl font-extrabold leading-tight text-neutral-900">{{ $product->name }}</h1>

                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm">
                    <span class="flex items-center gap-1.5">
                        <span class="flex items-center gap-0.5 text-amber-400">
                            @for ($i = 1; $i <= 5; $i++)
                                <x-icon :name="$i <= round($product->rating) ? 'star' : 'star-outline'" class="h-4 w-4" />
                            @endfor
                        </span>
                        <span class="font-bold text-neutral-900">{{ number_format($product->rating, 1) }}</span>
                        <span class="text-neutral-400">({{ $ratingCount }} review)</span>
                    </span>
                    <span class="h-4 w-px bg-neutral-200"></span>
                    <span class="text-neutral-500"><span class="font-semibold text-neutral-900">{{ number_format($product->sold, 0, ',', '.') }}</span> terjual</span>
                    <span class="h-4 w-px bg-neutral-200"></span>
                    <span class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ $product->stock > 0 ? "Stok: {$product->stock}" : 'Stok Habis' }}
                    </span>
                </div>

                <div class="mt-6 flex flex-wrap items-end gap-3 rounded-2xl bg-neutral-50 px-5 py-4">
                    <span class="text-4xl font-black text-neutral-900">{{ $product->price_label }}</span>
                    @if ($product->is_discounted)
                        <span class="pb-1 text-base text-neutral-400 line-through">{{ 'Rp ' . number_format($product->compare_price, 0, ',', '.') }}</span>
                        <span class="pb-1 text-sm font-semibold text-red-500">Hemat {{ 'Rp ' . number_format($product->compare_price - $product->price, 0, ',', '.') }}</span>
                    @endif
                </div>

                <form action="{{ route('cart.add') }}" method="POST" class="mt-7 space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if (! empty($product->sizes))
                        <div>
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-bold text-neutral-900">Pilih Ukuran</label>
                                <span class="text-xs text-neutral-400">Wajib dipilih</span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($product->sizes as $size)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="size" value="{{ $size }}" class="peer sr-only" required>
                                        <span class="grid h-11 min-w-11 place-items-center rounded-xl border border-neutral-200 px-3 text-sm font-semibold text-neutral-700 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-600 hover:border-neutral-900">
                                            {{ $size }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (! empty($product->colors))
                        <div>
                            <label class="text-sm font-bold text-neutral-900">Pilih Warna</label>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($product->colors as $color)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="color" value="{{ $color }}" class="peer sr-only" required>
                                        <span class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 px-4 py-2.5 text-sm font-semibold text-neutral-700 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-600 hover:border-neutral-900">
                                            {{ $color }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="text-sm font-bold text-neutral-900">Jumlah</label>
                        <div class="mt-3 flex flex-wrap items-center gap-4">
                            <div data-qty class="inline-flex items-center rounded-xl border border-neutral-200 bg-white">
                                <button type="button" data-qty-decrease class="grid h-11 w-11 place-items-center text-neutral-600 hover:text-brand-600">
                                    <x-icon name="minus" class="h-4 w-4" />
                                </button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ max($product->stock, 1) }}"
                                    class="h-11 w-14 border-x border-neutral-200 text-center text-sm font-bold outline-none">
                                <button type="button" data-qty-increase class="grid h-11 w-11 place-items-center text-neutral-600 hover:text-brand-600">
                                    <x-icon name="plus" class="h-4 w-4" />
                                </button>
                            </div>
                            <span class="text-sm text-neutral-500">Tersisa {{ $product->stock }} unit</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" name="redirect" value="cart" @disabled($product->stock <= 0)
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-neutral-900 px-6 py-3.5 text-sm font-bold text-neutral-900 transition hover:bg-neutral-900 hover:text-white disabled:cursor-not-allowed disabled:opacity-40">
                            <x-icon name="cart" class="h-5 w-5" /> Tambah ke Keranjang
                        </button>
                        <button type="submit" name="redirect" value="checkout" @disabled($product->stock <= 0)
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-40">
                            Beli Sekarang
                        </button>
                    </div>
                </form>

                <div class="mt-5 flex items-center gap-3">
                    <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 px-4 py-2.5 text-sm font-semibold text-neutral-700 transition hover:border-brand-500 hover:text-brand-600">
                            <x-icon name="heart" class="h-5 w-5 {{ $wishlisted ? 'fill-brand-500 text-brand-500' : '' }}" />
                            {{ $wishlisted ? 'Tersimpan di Wishlist' : 'Simpan ke Wishlist' }}
                        </button>
                    </form>
                </div>

                <div class="mt-7 grid gap-4 rounded-2xl border border-neutral-100 bg-white p-5 shadow-sm sm:grid-cols-3">
                    @foreach ([
                        ['truck', 'Pengiriman', 'JNE, J&T, SiCepat, POS'],
                        ['shield', 'Garansi', '100% original & bergaransi'],
                        ['refresh', 'Retur', '7 hari pengembalian'],
                    ] as [$icon, $title, $desc])
                        <div class="flex items-start gap-3">
                            <x-icon :name="$icon" class="mt-0.5 h-6 w-6 shrink-0 text-brand-500" />
                            <div>
                                <p class="text-sm font-bold text-neutral-900">{{ $title }}</p>
                                <p class="text-xs text-neutral-500">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-14 grid gap-8 lg:grid-cols-[1fr_360px]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-neutral-100 bg-white p-7 shadow-sm">
                    <h2 class="text-lg font-extrabold text-neutral-900">Deskripsi Produk</h2>
                    <p class="mt-4 whitespace-pre-line leading-relaxed text-neutral-600">{{ $product->description }}</p>

                    @if ($product->specification)
                        <h3 class="mt-6 text-sm font-bold uppercase tracking-wide text-neutral-900">Spesifikasi</h3>
                        <p class="mt-2 whitespace-pre-line leading-relaxed text-neutral-600">{{ $product->specification }}</p>
                    @endif

                    <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-neutral-100 pt-6 text-sm sm:grid-cols-3">
                        <div>
                            <dt class="text-neutral-400">Kategori</dt>
                            <dd class="mt-1 font-semibold text-neutral-900">{{ $product->category->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-neutral-400">Stok</dt>
                            <dd class="mt-1 font-semibold text-neutral-900">{{ $product->stock }} unit</dd>
                        </div>
                        <div>
                            <dt class="text-neutral-400">Terjual</dt>
                            <dd class="mt-1 font-semibold text-neutral-900">{{ number_format($product->sold, 0, ',', '.') }} produk</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-3xl border border-neutral-100 bg-white p-7 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-extrabold text-neutral-900">Ulasan Pembeli</h2>
                        <span class="text-sm text-neutral-500">{{ $ratingCount }} ulasan</span>
                    </div>

                    @forelse ($product->reviews->take(5) as $review)
                        <div class="mt-5 border-t border-neutral-100 pt-5 first:border-0 first:pt-0">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 place-items-center rounded-full bg-neutral-900 text-sm font-bold text-white">
                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-neutral-900">{{ $review->user->name ?? 'Pengguna' }}</p>
                                    <span class="flex items-center gap-0.5 text-amber-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-icon :name="$i <= $review->rating ? 'star' : 'star-outline'" class="h-3.5 w-3.5" />
                                        @endfor
                                    </span>
                                </div>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-neutral-600">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="mt-4 rounded-2xl bg-neutral-50 px-5 py-8 text-center text-sm text-neutral-500">
                            Belum ada ulasan untuk produk ini. Jadilah yang pertama setelah membeli!
                        </p>
                    @endforelse
                </div>
            </div>

            <aside class="lg:sticky lg:top-32 lg:self-start">
                <h2 class="text-lg font-extrabold text-neutral-900">Produk yang Direkomendasikan</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($related as $item)
                        <a href="{{ route('products.show', $item->slug) }}"
                            class="flex gap-4 rounded-2xl border border-neutral-100 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="h-20 w-20 shrink-0 rounded-xl object-cover">
                            <div class="min-w-0">
                                <p class="line-clamp-2 text-sm font-semibold text-neutral-900">{{ $item->name }}</p>
                                <span class="mt-1 flex items-center gap-1 text-xs text-amber-400">
                                    <x-icon name="star" class="h-3.5 w-3.5" />
                                    <span class="font-medium text-neutral-600">{{ number_format($item->rating, 1) }}</span>
                                </span>
                                <p class="mt-1 text-sm font-extrabold text-brand-600">{{ $item->price_label }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </aside>
        </div>
    </div>
@endsection
