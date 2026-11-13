@extends('layouts.app')

@section('title', 'Keranjang Belanja - GoSport')

@section('content')
    <div class="border-b border-neutral-200 bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="font-medium text-neutral-900">Keranjang</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-neutral-900">Keranjang Belanja</h1>
        </div>
    </div>

    <div class="container-page py-10">
        @if ($items->isEmpty())
            <div class="rounded-3xl border border-dashed border-neutral-200 bg-white py-20 text-center">
                <x-icon name="cart" class="mx-auto h-14 w-14 text-neutral-300" />
                <h2 class="mt-4 text-xl font-bold text-neutral-900">Keranjang kamu masih kosong</h2>
                <p class="mt-1 text-sm text-neutral-500">Yuk mulai belanja perlengkapan olahraga favoritmu.</p>
                <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-xl bg-brand-500 px-7 py-3.5 text-sm font-bold text-white transition hover:bg-brand-600">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
                <div>
                    <form id="cart-form" action="{{ route('cart.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <label class="flex w-fit cursor-pointer items-center gap-2.5 text-sm font-semibold text-neutral-700">
                            <input type="checkbox" form="checkout-form" name="items[]" value="__all__" data-cart-select-all
                                @checked($items->every(fn ($item) => in_array($item->id, old('items', $items->modelKeys()))))
                                class="h-4 w-4 rounded border-neutral-300 text-brand-500 focus:ring-brand-500">
                            Pilih Semua
                        </label>

                        @foreach ($items as $item)
                            <div class="flex flex-col gap-4 rounded-2xl border border-neutral-100 bg-white p-4 shadow-sm sm:flex-row sm:items-center">
                                <label class="flex shrink-0 cursor-pointer items-center sm:h-24">
                                    <input type="checkbox" form="checkout-form" name="items[]" value="{{ $item->id }}"
                                        data-cart-item data-subtotal="{{ $item->subtotal }}" data-quantity="{{ $item->quantity }}"
                                        @checked(in_array($item->id, old('items', $items->modelKeys())))
                                        class="h-5 w-5 rounded border-neutral-300 text-brand-500 focus:ring-brand-500">
                                </label>

                                <a href="{{ route('products.show', $item->product->slug) }}" class="shrink-0">
                                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="h-24 w-24 rounded-xl object-cover">
                                </a>

                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium uppercase tracking-wide text-neutral-400">{{ $item->product->category->name ?? '-' }}</p>
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="line-clamp-2 text-sm font-bold text-neutral-900 hover:text-brand-600">
                                        {{ $item->product->name }}
                                    </a>
                                    <div class="mt-1.5 flex flex-wrap items-center gap-2 text-xs text-neutral-500">
                                        @if ($item->size)
                                            <span class="rounded-md bg-neutral-100 px-2 py-0.5 font-medium">Ukuran: {{ $item->size }}</span>
                                        @endif
                                        @if ($item->color)
                                            <span class="rounded-md bg-neutral-100 px-2 py-0.5 font-medium">Warna: {{ $item->color }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm font-bold text-brand-600">{{ $item->product->price_label }}</p>
                                </div>

                                <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                    <div data-qty class="inline-flex items-center rounded-xl border border-neutral-200">
                                        <button type="button" data-qty-decrease class="grid h-9 w-9 place-items-center text-neutral-600 hover:text-brand-600">
                                            <x-icon name="minus" class="h-3.5 w-3.5" />
                                        </button>
                                        <input type="number" name="quantity[{{ $item->id }}]" value="{{ $item->quantity }}" min="1" max="{{ max($item->product->stock, 1) }}"
                                            class="h-9 w-12 border-x border-neutral-200 text-center text-sm font-bold outline-none">
                                        <button type="button" data-qty-increase class="grid h-9 w-9 place-items-center text-neutral-600 hover:text-brand-600">
                                            <x-icon name="plus" class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                    <p class="text-sm font-extrabold text-neutral-900">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                                    <button type="submit" form="remove-form-{{ $item->id }}" class="grid h-9 w-9 place-items-center rounded-lg text-neutral-400 transition hover:bg-red-50 hover:text-red-500" aria-label="Hapus">
                                        <x-icon name="trash" class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-600 hover:text-brand-600">
                                <x-icon name="arrow-left" class="h-4 w-4" /> Lanjut Belanja
                            </a>
                            <button type="submit" class="rounded-xl border border-neutral-200 px-5 py-2.5 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-100">
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
                    <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-extrabold text-neutral-900">Ringkasan Belanja</h2>

                        <dl class="mt-5 space-y-3 text-sm"
                            data-cart-summary
                            data-coupon-code="{{ $coupon->code ?? '' }}"
                            data-coupon-type="{{ $coupon->type ?? '' }}"
                            data-coupon-value="{{ $coupon->value ?? 0 }}"
                            data-coupon-min="{{ $coupon->min_order ?? 0 }}"
                            data-coupon-active="{{ ($coupon?->is_active ?? false) ? 1 : 0 }}">
                            <div class="flex justify-between">
                                <dt class="text-neutral-500">Subtotal (<span data-cart-count>{{ $items->sum('quantity') }}</span> barang)</dt>
                                <dd class="font-semibold text-neutral-900" data-cart-subtotal>{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between text-green-600 @if ($discount <= 0) hidden @endif" data-cart-discount-row>
                                <dt>Diskon (<span data-cart-discount-code>{{ $coupon->code ?? '' }}</span>)</dt>
                                <dd class="font-semibold" data-cart-discount>- {{ 'Rp ' . number_format($discount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-neutral-100 pt-3 text-base">
                                <dt class="font-bold text-neutral-900">Total</dt>
                                <dd class="font-black text-brand-600" data-cart-total>{{ 'Rp ' . number_format(max($subtotal - $discount, 0), 0, ',', '.') }}</dd>
                            </div>
                        </dl>

                        <form id="checkout-form" action="{{ route('checkout.index') }}" method="GET" data-checkout-form>
                            <button type="submit" data-cart-checkout
                                class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600">
                                <span data-cart-checkout-label>Checkout</span> <x-icon name="arrow-right" class="h-5 w-5" />
                            </button>
                            <p class="mt-3 hidden text-center text-xs font-semibold text-red-500" data-cart-error>
                                Silakan pilih minimal satu produk untuk checkout.
                            </p>
                        </form>
                    </div>

                    <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                        <h2 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-neutral-900">
                            <x-icon name="ticket" class="h-4 w-4 text-brand-500" /> Kode Voucher
                        </h2>

                        @if ($coupon)
                            <div class="mt-4 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                                <div>
                                    <p class="text-sm font-bold text-green-700">{{ $coupon->code }}</p>
                                    <p class="text-xs text-green-600">Voucher aktif &middot; hemat {{ $coupon->label }}</p>
                                </div>
                                <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-red-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.coupon.apply') }}" method="POST" class="mt-4 flex gap-2">
                                @csrf
                                <input type="text" name="code" placeholder="Masukkan kode" required
                                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm uppercase outline-none focus:border-brand-500 focus:bg-white">
                                <button type="submit" class="shrink-0 rounded-xl bg-neutral-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-brand-500">
                                    Pakai
                                </button>
                            </form>
                            <p class="mt-3 text-xs text-neutral-400">Coba: <span class="font-semibold text-neutral-600">GOSPORT10</span> atau <span class="font-semibold text-neutral-600">GOSPORT50</span></p>
                        @endif
                    </div>
                </aside>
            </div>
        @endif
    </div>
@endsection
