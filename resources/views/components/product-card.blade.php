@props(['product'])

@php
    $badgeStyles = [
        'baru' => 'bg-[#0A6CFF] text-white',
        'terlaris' => 'bg-[#0A2A5E] text-white',
        'promo' => 'bg-[#FF7A00] text-[#10213F]',
    ];
    $badgeLabels = ['baru' => 'Baru', 'terlaris' => 'Terlaris', 'promo' => 'Promo'];
    $wishlisted = $product->isWishlisted();
@endphp

<div class="group relative flex flex-col overflow-hidden rounded-2xl border border-[#8CC4FF] bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#0A6CFF] hover:shadow-xl">
    <a href="{{ route('products.show', $product->slug) }}" class="relative block aspect-square overflow-hidden bg-[#E6F1FF]">
        <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy"
            class="h-full w-full object-cover transition duration-500 group-hover:scale-110">

        <div class="absolute left-3 top-3 flex flex-col gap-1.5">
            @if ($product->badge)
                <span class="rounded-full px-2.5 py-1 text-[11px] font-black uppercase tracking-wide {{ $badgeStyles[$product->badge] ?? 'bg-[#0A2A5E] text-white' }}">
                    {{ $badgeLabels[$product->badge] ?? $product->badge }}
                </span>
            @endif
            @if ($product->is_discounted)
                {{-- Aksen: badge diskon #FF7A00 dengan teks #10213F --}}
                <span class="rounded-full bg-[#FF7A00] px-2.5 py-1 text-[11px] font-black text-[#10213F] shadow-sm">
                    -{{ $product->discount_percent }}%
                </span>
            @endif
        </div>

        @if ($product->stock <= 0)
            {{-- Merah #E53935 hanya untuk stok habis --}}
            <span class="absolute inset-0 grid place-items-center bg-[#E53935]/85 text-sm font-black uppercase tracking-wider text-white">
                Stok Habis
            </span>
        @endif
    </a>

    <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="absolute right-3 top-3">
        @csrf
        <button type="submit" aria-label="Wishlist"
            class="grid h-9 w-9 place-items-center rounded-full bg-white/95 text-[#0A6CFF] shadow-sm backdrop-blur transition hover:bg-[#E6F1FF] hover:text-[#3D9BFF]">
            <x-icon name="heart" class="h-5 w-5 {{ $wishlisted ? 'fill-[#0A6CFF] text-[#0A6CFF]' : '' }}" />
        </button>
    </form>

    <div class="flex flex-1 flex-col p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-[#0A6CFF]">{{ $product->category->name ?? '-' }}</p>
        <h3 class="mt-1 line-clamp-2 text-sm font-bold leading-snug text-[#10213F]">
            <a href="{{ route('products.show', $product->slug) }}" class="transition hover:text-[#0A6CFF]">{{ $product->name }}</a>
        </h3>

        <div class="mt-2 flex items-center gap-2 text-xs text-neutral-500">
            <span class="flex items-center gap-0.5 text-amber-500">
                @for ($i = 1; $i <= 5; $i++)
                    <x-icon :name="$i <= round($product->rating) ? 'star' : 'star-outline'" class="h-3.5 w-3.5" />
                @endfor
            </span>
            <span class="font-bold text-[#10213F]">{{ number_format($product->rating, 1) }}</span>
            <span>&middot;</span>
            <span>{{ number_format($product->sold, 0, ',', '.') }} terjual</span>
        </div>

        <div class="mt-3 flex flex-wrap items-baseline gap-2">
            <span class="text-lg font-black text-[#0A2A5E]">{{ $product->price_label }}</span>
            @if ($product->is_discounted)
                <span class="text-xs text-neutral-400 line-through">{{ 'Rp ' . number_format($product->compare_price, 0, ',', '.') }}</span>
            @endif
        </div>

        <div class="mt-4 grid grid-cols-2 gap-2 pt-1">
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                {{-- Tombol sekunder: Primer (#0A6CFF) hover (#3D9BFF) --}}
                <button type="submit" @disabled($product->stock <= 0)
                    class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-[#0A6CFF] px-3 py-2.5 text-xs font-bold text-white transition hover:bg-[#3D9BFF] disabled:cursor-not-allowed disabled:bg-neutral-300">
                    <x-icon name="cart" class="h-4 w-4" /> Keranjang
                </button>
            </form>
            <a href="{{ route('products.show', $product->slug) }}"
                class="flex items-center justify-center gap-1.5 rounded-xl border border-[#8CC4FF] bg-[#F4F8FF] px-3 py-2.5 text-xs font-bold text-[#0A2A5E] transition hover:bg-[#0A2A5E] hover:text-white">
                Detail
            </a>
        </div>
    </div>
</div>
