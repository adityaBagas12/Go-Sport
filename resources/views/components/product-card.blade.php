@props(['product'])

@php
    $badgeStyles = [
        'baru' => 'bg-blue-600 text-white',
        'terlaris' => 'bg-neutral-900 text-white',
        'promo' => 'bg-brand-500 text-white',
    ];
    $badgeLabels = ['baru' => 'Baru', 'terlaris' => 'Terlaris', 'promo' => 'Promo'];
    $wishlisted = $product->isWishlisted();
@endphp

<div class="group relative flex flex-col overflow-hidden rounded-2xl border border-neutral-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
    <a href="{{ route('products.show', $product->slug) }}" class="relative block aspect-square overflow-hidden bg-neutral-100">
        <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy"
            class="h-full w-full object-cover transition duration-500 group-hover:scale-110">

        <div class="absolute left-3 top-3 flex flex-col gap-1.5">
            @if ($product->badge)
                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide {{ $badgeStyles[$product->badge] ?? 'bg-neutral-900 text-white' }}">
                    {{ $badgeLabels[$product->badge] ?? $product->badge }}
                </span>
            @endif
            @if ($product->is_discounted)
                <span class="rounded-full bg-red-500 px-2.5 py-1 text-[11px] font-bold text-white">
                    -{{ $product->discount_percent }}%
                </span>
            @endif
        </div>

        @if ($product->stock <= 0)
            <span class="absolute inset-0 grid place-items-center bg-neutral-900/60 text-sm font-bold uppercase tracking-wider text-white">
                Stok Habis
            </span>
        @endif
    </a>

    <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="absolute right-3 top-3">
        @csrf
        <button type="submit" aria-label="Wishlist"
            class="grid h-9 w-9 place-items-center rounded-full bg-white/90 text-neutral-600 shadow-sm backdrop-blur transition hover:bg-white hover:text-brand-500">
            <x-icon name="heart" class="h-5 w-5 {{ $wishlisted ? 'fill-brand-500 text-brand-500' : '' }}" />
        </button>
    </form>

    <div class="flex flex-1 flex-col p-4">
        <p class="text-xs font-medium uppercase tracking-wide text-neutral-400">{{ $product->category->name ?? '-' }}</p>
        <h3 class="mt-1 line-clamp-2 text-sm font-semibold leading-snug text-neutral-900">
            <a href="{{ route('products.show', $product->slug) }}" class="transition hover:text-brand-600">{{ $product->name }}</a>
        </h3>

        <div class="mt-2 flex items-center gap-2 text-xs text-neutral-500">
            <span class="flex items-center gap-0.5 text-amber-400">
                @for ($i = 1; $i <= 5; $i++)
                    <x-icon :name="$i <= round($product->rating) ? 'star' : 'star-outline'" class="h-3.5 w-3.5" />
                @endfor
            </span>
            <span class="font-medium text-neutral-700">{{ number_format($product->rating, 1) }}</span>
            <span>&middot;</span>
            <span>{{ number_format($product->sold, 0, ',', '.') }} terjual</span>
        </div>

        <div class="mt-3 flex flex-wrap items-baseline gap-2">
            <span class="text-lg font-extrabold text-neutral-900">{{ $product->price_label }}</span>
            @if ($product->is_discounted)
                <span class="text-xs text-neutral-400 line-through">{{ 'Rp ' . number_format($product->compare_price, 0, ',', '.') }}</span>
            @endif
        </div>

        <div class="mt-4 grid grid-cols-2 gap-2 pt-1">
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" @disabled($product->stock <= 0)
                    class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-neutral-900 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-brand-500 disabled:cursor-not-allowed disabled:bg-neutral-300">
                    <x-icon name="cart" class="h-4 w-4" /> Keranjang
                </button>
            </form>
            <a href="{{ route('products.show', $product->slug) }}"
                class="flex items-center justify-center gap-1.5 rounded-xl border border-neutral-200 px-3 py-2.5 text-xs font-semibold text-neutral-700 transition hover:border-neutral-900 hover:bg-neutral-900 hover:text-white">
                Detail
            </a>
        </div>
    </div>
</div>
