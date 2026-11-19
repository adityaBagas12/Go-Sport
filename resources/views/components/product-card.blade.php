@props(['product'])

@php
    $badgeStyles = [
        'baru' => 'bg-[#2F4F3E] text-white',
        'terlaris' => 'bg-[#4F7A63] text-white',
        'promo' => 'bg-[#4F7A63] text-white',
    ];
    $badgeLabels = ['baru' => 'Baru', 'terlaris' => 'Terlaris', 'promo' => 'Promo'];
    $wishlisted = $product->isWishlisted();
@endphp

<div class="group relative flex flex-col overflow-hidden rounded-2xl border border-[#D5E0D8] bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#4F7A63] hover:shadow-xl">
    <a href="{{ route('products.show', $product->slug) }}" class="relative block aspect-square overflow-hidden bg-[#F6F9F7]">
        <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy"
            class="h-full w-full object-cover transition duration-500 group-hover:scale-110">

        <div class="absolute left-3 top-3 flex flex-col gap-1.5">
            @if ($product->badge)
                <span class="rounded-full px-2.5 py-1 text-[11px] font-black uppercase tracking-wide {{ $badgeStyles[$product->badge] ?? 'bg-[#2F4F3E] text-white' }}">
                    {{ $badgeLabels[$product->badge] ?? $product->badge }}
                </span>
            @endif
            @if ($product->is_discounted)
                <span class="rounded-full bg-[#4F7A63] px-2.5 py-1 text-[11px] font-black text-white shadow-sm">
                    -{{ $product->discount_percent }}%
                </span>
            @endif
        </div>

        @if ($product->stock <= 0)
            <span class="absolute inset-0 grid place-items-center bg-[#E53935]/85 text-sm font-black uppercase tracking-wider text-white">
                Stok Habis
            </span>
        @endif
    </a>

    <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="absolute right-3 top-3">
        @csrf
        <button type="submit" aria-label="Wishlist"
            class="grid h-9 w-9 place-items-center rounded-full bg-white/95 text-[#5A6B61] shadow-sm backdrop-blur transition hover:bg-[#F6F9F7] hover:text-[#4F7A63]">
            <x-icon name="heart" class="h-5 w-5 {{ $wishlisted ? 'fill-[#4F7A63] text-[#4F7A63]' : '' }}" />
        </button>
    </form>

    <div class="flex flex-1 flex-col p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-[#4F7A63]">{{ $product->category->name ?? '-' }}</p>
        <h3 class="mt-1 line-clamp-2 text-sm font-bold leading-snug text-[#1E2B24]">
            <a href="{{ route('products.show', $product->slug) }}" class="transition hover:text-[#4F7A63]">{{ $product->name }}</a>
        </h3>

        <div class="mt-2 flex items-center gap-2 text-xs text-[#5A6B61]">
            @if ($product->rating > 0)
                <span class="flex items-center gap-0.5 text-amber-500">
                    @for ($i = 1; $i <= 5; $i++)
                        <x-icon :name="$i <= round($product->rating) ? 'star' : 'star-outline'" class="h-3.5 w-3.5" />
                    @endfor
                </span>
                <span class="font-bold text-[#1E2B24]">{{ number_format($product->rating, 1) }}</span>
                <span>&middot;</span>
            @endif
            <span>{{ number_format($product->sold, 0, ',', '.') }} terjual</span>
        </div>

        <div class="mt-3 flex flex-wrap items-baseline gap-2">
            <span class="text-lg font-black text-[#1E2B24]">{{ $product->price_label }}</span>
            @if ($product->is_discounted)
                <span class="text-xs text-[#5A6B61] line-through">{{ 'Rp ' . number_format($product->compare_price, 0, ',', '.') }}</span>
            @endif
        </div>

        <div class="mt-4 grid grid-cols-2 gap-2 pt-1">
            @if ($product->hasVariants())
                <button type="button"
                    data-quick-variant
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-image="{{ $product->image }}"
                    data-product-price="{{ $product->price_label }}"
                    data-product-raw-price="{{ $product->price }}"
                    data-product-sizes='@json($product->sizes ?? [])'
                    data-product-colors='@json($product->colors ?? [])'
                    data-product-variants='@json($product->variants)'
                    @disabled($product->stock <= 0)
                    class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-[#4F7A63] px-3 py-2.5 text-xs font-bold text-white transition hover:bg-[#3E6350] disabled:cursor-not-allowed disabled:bg-neutral-300">
                    <x-icon name="cart" class="h-4 w-4" /> Keranjang
                </button>
            @else
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" @disabled($product->stock <= 0)
                        class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-[#4F7A63] px-3 py-2.5 text-xs font-bold text-white transition hover:bg-[#3E6350] disabled:cursor-not-allowed disabled:bg-neutral-300">
                        <x-icon name="cart" class="h-4 w-4" /> Keranjang
                    </button>
                </form>
            @endif
            <a href="{{ route('products.show', $product->slug) }}"
                class="flex items-center justify-center gap-1.5 rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-2.5 text-xs font-bold text-[#1E2B24] transition hover:bg-[#2F4F3E] hover:text-white">
                Detail
            </a>
        </div>
    </div>
</div>
