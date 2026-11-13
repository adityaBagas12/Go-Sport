@extends('layouts.app')

@section('title', 'Keranjang Belanja - GoSport')

@section('content')
    <div class="border-b border-[#8CC4FF]/40 bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-[#0A6CFF]">
                <a href="{{ route('home') }}" class="hover:text-[#3D9BFF]">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4 text-[#8CC4FF]" />
                <span class="font-bold text-[#0A2A5E]">Keranjang</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-[#0A2A5E]">Keranjang Belanja</h1>
        </div>
    </div>

    <div class="container-page py-10">
        @if ($items->isEmpty())
            <div class="rounded-3xl border border-dashed border-[#8CC4FF] bg-white py-20 text-center">
                <x-icon name="cart" class="mx-auto h-14 w-14 text-[#8CC4FF]" />
                <h2 class="mt-4 text-xl font-bold text-[#0A2A5E]">Keranjang kamu masih kosong</h2>
                <p class="mt-1 text-sm text-neutral-500">Yuk mulai belanja perlengkapan olahraga favoritmu.</p>
                <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-xl bg-[#0A6CFF] px-7 py-3.5 text-sm font-bold text-white transition hover:bg-[#3D9BFF]">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
                <div>
                    <form id="cart-form" action="{{ route('cart.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <label class="flex w-fit cursor-pointer items-center gap-2.5 text-sm font-semibold text-[#10213F]">
                            <input type="checkbox" form="checkout-form" name="items[]" value="__all__" data-cart-select-all
                                @checked($items->every(fn ($item) => in_array($item->id, old('items', $items->modelKeys()))))
                                class="h-4 w-4 rounded border-[#8CC4FF] text-[#0A6CFF] focus:ring-[#0A6CFF]">
                            Pilih Semua
                        </label>

                        @foreach ($items as $item)
                            <div class="flex flex-col gap-4 rounded-2xl border border-[#8CC4FF] bg-white p-4 shadow-sm sm:flex-row sm:items-center">
                                <label class="flex shrink-0 cursor-pointer items-center sm:h-24">
                                    <input type="checkbox" form="checkout-form" name="items[]" value="{{ $item->id }}"
                                        data-cart-item data-subtotal="{{ $item->subtotal }}" data-quantity="{{ $item->quantity }}"
                                        @checked(in_array($item->id, old('items', $items->modelKeys())))
                                        class="h-5 w-5 rounded border-[#8CC4FF] text-[#0A6CFF] focus:ring-[#0A6CFF]">
                                </label>

                                <a href="{{ route('products.show', $item->product->slug) }}" class="shrink-0">
                                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="h-24 w-24 rounded-xl border border-[#8CC4FF]/40 object-cover">
                                </a>

                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-[#0A6CFF]">{{ $item->product->category->name ?? '-' }}</p>
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="line-clamp-2 text-sm font-bold text-[#10213F] hover:text-[#0A6CFF]">
                                        {{ $item->product->name }}
                                    </a>
                                    <div class="mt-1.5 flex flex-wrap items-center gap-2 text-xs text-neutral-500">
                                        @if ($item->size)
                                            <span class="rounded-md bg-[#E6F1FF] px-2 py-0.5 font-medium text-[#0A2A5E]">Ukuran: {{ $item->size }}</span>
                                        @endif
                                        @if ($item->color)
                                            <span class="rounded-md bg-[#E6F1FF] px-2 py-0.5 font-medium text-[#0A2A5E]">Warna: {{ $item->color }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm font-bold text-[#0A2A5E]">{{ $item->product->price_label }}</p>
                                </div>

                                <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                    <div data-qty class="inline-flex items-center rounded-xl border border-[#8CC4FF] bg-[#F4F8FF]">
                                        <button type="button" data-qty-decrease class="grid h-9 w-9 place-items-center text-neutral-600 hover:text-[#0A6CFF]">
                                            <x-icon name="minus" class="h-3.5 w-3.5" />
                                        </button>
                                        <input type="number" name="quantity[{{ $item->id }}]" value="{{ $item->quantity }}" min="1" max="{{ max($item->product->stock, 1) }}"
                                            class="h-9 w-12 border-x border-[#8CC4FF] bg-white text-center text-sm font-bold text-[#10213F] outline-none">
                                        <button type="button" data-qty-increase class="grid h-9 w-9 place-items-center text-neutral-600 hover:text-[#0A6CFF]">
                                            <x-icon name="plus" class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                    <p class="text-sm font-extrabold text-[#0A2A5E]">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                                    <button type="submit" form="remove-form-{{ $item->id }}" class="grid h-9 w-9 place-items-center rounded-lg text-neutral-400 transition hover:bg-red-50 hover:text-[#E53935]" aria-label="Hapus">
                                        <x-icon name="trash" class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0A6CFF] hover:text-[#3D9BFF]">
                                <x-icon name="arrow-left" class="h-4 w-4" /> Lanjut Belanja
                            </a>
                            <button type="submit" class="rounded-xl border border-[#8CC4FF] bg-white px-5 py-2.5 text-sm font-semibold text-[#0A2A5E] transition hover:bg-[#E6F1FF]">
                                Perbarui Keranjang
                            </button>
                        </div>
                    </form>

                    @foreach ($items as $item)
                        <form id="remove-form-{{ $item->id }}" action="{{ route('cart.remove', $item->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                </div>

                <aside class="space-y-5 lg:sticky lg:top-32 lg:self-start">
                    <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-extrabold text-[#0A2A5E]">Ringkasan Belanja</h2>

                        <dl class="mt-5 space-y-3 text-sm"
                            data-cart-summary
                            data-coupon-code="{{ $coupon->code ?? '' }}"
                            data-coupon-type="{{ $coupon->type ?? '' }}"
                            data-coupon-value="{{ $coupon->value ?? 0 }}"
                            data-coupon-min="{{ $coupon->min_order ?? 0 }}"
                            data-coupon-active="{{ ($coupon?->is_active ?? false) ? 1 : 0 }}">
                            <div class="flex justify-between">
                                <dt class="text-neutral-500">Subtotal (<span data-cart-count>{{ $items->sum('quantity') }}</span> barang)</dt>
                                <dd class="font-bold text-[#10213F]" data-cart-subtotal>{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between text-[#0A6CFF] @if ($discount <= 0) hidden @endif" data-cart-discount-row>
                                <dt>Diskon (<span data-cart-discount-code>{{ $coupon->code ?? '' }}</span>)</dt>
                                <dd class="font-bold text-[#FF7A00]" data-cart-discount>- {{ 'Rp ' . number_format($discount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-[#8CC4FF]/40 pt-3 text-base">
                                <dt class="font-bold text-[#10213F]">Total</dt>
                                <dd class="font-black text-[#0A2A5E]" data-cart-total>{{ 'Rp ' . number_format(max($subtotal - $discount, 0), 0, ',', '.') }}</dd>
                            </div>
                        </dl>

                        {{-- Tombol Aksen: Checkout #FF7A00 dengan teks gelap #10213F --}}
                        <form id="checkout-form" action="{{ route('checkout.index') }}" method="GET" data-checkout-form>
                            <button type="submit" data-cart-checkout
                                class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-[#FF7A00] px-6 py-3.5 text-sm font-black text-[#10213F] shadow-lg shadow-[#FF7A00]/30 transition hover:bg-[#ff8f26]">
                                <span data-cart-checkout-label>Checkout</span> <x-icon name="arrow-right" class="h-5 w-5 text-[#10213F]" />
                            </button>
                            <p class="mt-3 hidden text-center text-xs font-semibold text-[#E53935]" data-cart-error>
                                Silakan pilih minimal satu produk untuk checkout.
                            </p>
                        </form>
                    </div>

                    <div class="rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
                        <h2 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-[#0A2A5E]">
                            <x-icon name="ticket" class="h-4 w-4 text-[#0A6CFF]" /> Kode Voucher
                        </h2>

                        @if ($coupon)
                            <div class="mt-4 flex items-center justify-between rounded-xl border border-[#8CC4FF] bg-[#E6F1FF] px-4 py-3">
                                <div>
                                    <p class="text-sm font-bold text-[#0A2A5E]">{{ $coupon->code }}</p>
                                    <p class="text-xs text-[#0A6CFF]">Voucher aktif &middot; hemat {{ $coupon->label }}</p>
                                </div>
                                <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-[#E53935] hover:underline">Hapus</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.coupon.apply') }}" method="POST" class="mt-4 flex gap-2">
                                @csrf
                                <input type="text" name="code" placeholder="Masukkan kode" required
                                    class="w-full rounded-xl border border-[#8CC4FF] bg-[#F4F8FF] px-3 py-2.5 text-sm text-[#10213F] uppercase outline-none focus:border-[#0A6CFF] focus:bg-white focus:ring-2 focus:ring-[#8CC4FF]">
                                <button type="submit" class="shrink-0 rounded-xl bg-[#0A6CFF] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#3D9BFF]">
                                    Pakai
                                </button>
                            </form>
                            <p class="mt-3 text-xs text-neutral-400">Coba: <span class="font-bold text-[#0A6CFF]">GOSPORT10</span></p>
                        @endif
                    </div>
                </aside>
            </div>
        @endif
    </div>
@endsection
