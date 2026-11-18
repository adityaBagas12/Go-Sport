@extends('layouts.app')

@section('title', $product->name . ' - GoSport')
@section('meta_description', Str::limit(strip_tags($product->description), 150))

@section('content')
    @php
        $gallery = collect([$product->image])->merge($product->images->pluck('image'))->filter()->unique()->values();
        $wishlisted = $product->isWishlisted();
        $ratingCount = $product->reviews->count();
    @endphp

    <div class="border-b border-[#D5E0D8] bg-white">
        <div class="container-page py-5">
            <nav class="flex flex-wrap items-center gap-2 text-sm text-[#5A6B61]">
                <a href="{{ route('home') }}" class="hover:text-[#4F7A63]">Home</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <a href="{{ route('products.index') }}" class="hover:text-[#4F7A63]">Produk</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-[#4F7A63]">{{ $product->category->name }}</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="truncate font-medium text-[#1E2B24]">{{ Str::limit($product->name, 40) }}</span>
            </nav>
        </div>
    </div>

    <div class="container-page py-10">
        <div class="grid gap-10 lg:grid-cols-2">
            <div data-gallery class="lg:sticky lg:top-28 lg:self-start space-y-4">
                <div class="overflow-hidden rounded-3xl border border-[#D5E0D8] bg-white shadow-sm">
                    <img id="main-product-img" src="{{ $product->image }}" alt="{{ $product->name }}"
                        class="aspect-square w-full object-cover">
                </div>
                @if ($gallery->count() > 1)
                    <div class="flex flex-wrap gap-2.5">
                        @foreach ($gallery as $img)
                            <button type="button" onclick="document.getElementById('main-product-img').src = '{{ $img }}'"
                                class="h-16 w-16 overflow-hidden rounded-2xl border-2 border-[#D5E0D8] bg-white transition hover:border-[#4F7A63] focus:border-[#4F7A63]">
                                <img src="{{ $img }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($product->badge)
                        <span class="rounded-full bg-[#2F4F3E] px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">
                            {{ ['baru' => 'Baru', 'terlaris' => 'Terlaris', 'promo' => 'Promo'][$product->badge] ?? $product->badge }}
                        </span>
                    @endif
                    <span class="rounded-full bg-[#DCE8DF] px-3 py-1 text-xs font-bold text-[#1E2B24]">{{ $product->category->name }}</span>
                    @if ($product->is_discounted)
                        <span class="rounded-full bg-[#4F7A63] px-3 py-1 text-xs font-black text-white">Diskon {{ $product->discount_percent }}%</span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl font-extrabold leading-tight text-[#1E2B24]">{{ $product->name }}</h1>

                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm">
                    @if ($product->rating > 0 && $ratingCount > 0)
                        <span class="flex items-center gap-1.5">
                            <span class="flex items-center gap-0.5 text-amber-500">
                                @for ($i = 1; $i <= 5; $i++)
                                    <x-icon :name="$i <= round($product->rating) ? 'star' : 'star-outline'" class="h-4 w-4" />
                                @endfor
                            </span>
                            <span class="font-bold text-[#1E2B24]">{{ number_format($product->rating, 1) }}</span>
                            <span class="text-[#5A6B61]">({{ $ratingCount }} ulasan)</span>
                        </span>
                        <span class="h-4 w-px bg-[#D5E0D8]"></span>
                    @endif
                    <span class="text-[#5A6B61]"><span class="font-semibold text-[#1E2B24]">{{ number_format($product->sold, 0, ',', '.') }}</span> terjual</span>
                    <span class="h-4 w-px bg-[#D5E0D8]"></span>
                    <span class="{{ $product->stock > 0 ? 'text-[#4F7A63]' : 'text-[#E53935]' }} font-bold">
                        {{ $product->stock > 0 ? "Stok: {$product->stock}" : 'Stok Habis' }}
                    </span>
                </div>

                <div class="mt-6 flex flex-wrap items-end gap-3 rounded-2xl border border-[#D5E0D8] bg-[#DCE8DF]/40 px-5 py-4">
                    <span id="product-price-display" class="text-4xl font-black text-[#1E2B24]">{{ $product->price_label }}</span>
                    @if ($product->is_discounted)
                        <span class="pb-1 text-base text-[#5A6B61] line-through">{{ 'Rp ' . number_format($product->compare_price, 0, ',', '.') }}</span>
                        <span class="pb-1 text-sm font-bold text-[#4F7A63]">Hemat {{ 'Rp ' . number_format($product->compare_price - $product->price, 0, ',', '.') }}</span>
                    @endif
                </div>

                <form action="{{ route('cart.add') }}" method="POST" class="mt-7 space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if (! empty($product->sizes))
                        <div>
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-bold text-[#1E2B24]">Pilih Ukuran</label>
                                <span class="text-xs text-[#5A6B61]">Wajib dipilih</span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($product->sizes as $size)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="size" value="{{ $size }}" class="peer sr-only" {{ $loop->first ? 'checked' : '' }} required>
                                        <span class="grid h-11 min-w-11 place-items-center rounded-xl border border-[#D5E0D8] bg-white px-3 text-sm font-semibold text-[#1E2B24] transition peer-checked:border-[#4F7A63] peer-checked:bg-[#4F7A63]/10 peer-checked:text-[#4F7A63] hover:border-[#1E2B24]">
                                            {{ $size }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (! empty($product->colors))
                        <div>
                            <label class="text-sm font-bold text-[#1E2B24]">Pilih Warna</label>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($product->colors as $color)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="color" value="{{ $color }}" class="peer sr-only" {{ $loop->first ? 'checked' : '' }} required>
                                        <span class="inline-flex items-center gap-2 rounded-xl border border-[#D5E0D8] bg-white px-4 py-2.5 text-sm font-semibold text-[#1E2B24] transition peer-checked:border-[#4F7A63] peer-checked:bg-[#4F7A63]/10 peer-checked:text-[#4F7A63] hover:border-[#1E2B24]">
                                            {{ $color }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="text-sm font-bold text-[#1E2B24]">Jumlah</label>
                        <div class="mt-3 flex flex-wrap items-center gap-4">
                            <div data-qty class="inline-flex items-center rounded-xl border border-[#D5E0D8] bg-white">
                                <button type="button" data-qty-decrease class="grid h-11 w-11 place-items-center text-[#5A6B61] hover:text-[#4F7A63]">
                                    <x-icon name="minus" class="h-4 w-4" />
                                </button>
                                <input type="number" id="product-quantity-input" name="quantity" value="1" min="1" max="{{ max($product->stock, 1) }}"
                                    class="h-11 w-14 border-x border-[#D5E0D8] text-center text-sm font-bold text-[#1E2B24] outline-none">
                                <button type="button" data-qty-increase class="grid h-11 w-11 place-items-center text-[#5A6B61] hover:text-[#4F7A63]">
                                    <x-icon name="plus" class="h-4 w-4" />
                                </button>
                            </div>
                            <span id="product-stock-status" class="text-sm text-[#5A6B61]">
                                @if ($product->hasVariants())
                                    Pilih varian untuk melihat ketersediaan
                                @else
                                    Tersisa {{ $product->stock }} unit
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" id="btn-add-cart" name="redirect" value="cart" @disabled($product->stock <= 0)
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-[#4F7A63] bg-white px-6 py-3.5 text-sm font-bold text-[#4F7A63] transition hover:bg-[#4F7A63] hover:text-white disabled:cursor-not-allowed disabled:opacity-40">
                            <x-icon name="cart" class="h-5 w-5" /> Tambah ke Keranjang
                        </button>
                        <button type="submit" id="btn-buy-now" name="redirect" value="checkout" @disabled($product->stock <= 0)
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-[#4F7A63]/30 transition hover:bg-[#3E6350] disabled:cursor-not-allowed disabled:opacity-40">
                            Beli Sekarang
                        </button>
                    </div>
                </form>

                <div class="mt-5 flex items-center gap-3">
                    <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-[#D5E0D8] bg-white px-4 py-2.5 text-sm font-semibold text-[#1E2B24] transition hover:border-[#4F7A63] hover:text-[#4F7A63]">
                            <x-icon name="heart" class="h-5 w-5 {{ $wishlisted ? 'fill-[#4F7A63] text-[#4F7A63]' : 'text-[#5A6B61]' }}" />
                            {{ $wishlisted ? 'Tersimpan di Wishlist' : 'Simpan ke Wishlist' }}
                        </button>
                    </form>
                </div>

                <div class="mt-7 grid gap-4 rounded-2xl border border-[#D5E0D8] bg-white p-5 shadow-sm sm:grid-cols-3">
                    @foreach ([
                        ['truck', 'Pengiriman', 'JNE, J&T, SiCepat, POS'],
                        ['shield', 'Garansi', '100% original & bergaransi'],
                        ['refresh', 'Retur', '7 hari pengembalian'],
                    ] as [$icon, $title, $desc])
                        <div class="flex items-start gap-3">
                            <x-icon :name="$icon" class="mt-0.5 h-6 w-6 shrink-0 text-[#4F7A63]" />
                            <div>
                                <p class="text-sm font-bold text-[#1E2B24]">{{ $title }}</p>
                                <p class="text-xs text-[#5A6B61]">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-14 grid gap-8 lg:grid-cols-[1fr_360px]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-[#D5E0D8] bg-white p-7 shadow-sm">
                    <h2 class="text-lg font-extrabold text-[#1E2B24]">Deskripsi Produk</h2>
                    <p class="mt-4 whitespace-pre-line leading-relaxed text-[#1E2B24]">{{ $product->description }}</p>

                    @if ($product->specification)
                        <h3 class="mt-6 text-sm font-bold uppercase tracking-wide text-[#1E2B24]">Spesifikasi</h3>
                        <p class="mt-2 whitespace-pre-line leading-relaxed text-[#5A6B61]">{{ $product->specification }}</p>
                    @endif

                    <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-[#D5E0D8] pt-6 text-sm sm:grid-cols-3">
                        <div>
                            <dt class="text-[#5A6B61]">Kategori</dt>
                            <dd class="mt-1 font-semibold text-[#1E2B24]">{{ $product->category->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-[#5A6B61]">Stok</dt>
                            <dd class="mt-1 font-semibold text-[#1E2B24]">
                                {{ $product->stock }} unit
                                @if ($product->hasVariants())
                                    <span class="block text-xs font-normal text-[#5A6B61]">({{ $product->variants->count() }} pilihan varian)</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[#5A6B61]">Terjual</dt>
                            <dd class="mt-1 font-semibold text-[#1E2B24]">{{ number_format($product->sold, 0, ',', '.') }} produk</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-3xl border border-[#D5E0D8] bg-white p-7 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-extrabold text-[#1E2B24]">Ulasan Pembeli</h2>
                        <span class="text-sm text-[#5A6B61]">{{ $ratingCount }} ulasan</span>
                    </div>

                    @php
                        $userEligibleOrder = auth()->check()
                            ? auth()->user()->orders()
                                ->where(fn($q) => $q->where('payment_status', 'paid')->orWhereIn('status', ['diproses', 'dikirim', 'selesai']))
                                ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                                ->latest()
                                ->first()
                            : null;

                        $userExistingReview = ($userEligibleOrder && auth()->check())
                            ? \App\Models\Review::where('user_id', auth()->id())
                                ->where('product_id', $product->id)
                                ->where('order_id', $userEligibleOrder->id)
                                ->first()
                            : null;
                    @endphp

                    @if ($userEligibleOrder)
                        <div class="mt-5 rounded-2xl border border-[#D5E0D8] bg-[#F6F9F7] p-5">
                            @if ($userExistingReview)
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold text-[#1E2B24]">Ulasan Anda sebagai Pembeli Terverifikasi</p>
                                    <span class="flex items-center gap-1 text-sm font-bold text-amber-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-icon :name="$i <= $userExistingReview->rating ? 'star' : 'star-outline'" class="h-4 w-4" />
                                        @endfor
                                        <span class="text-[#1E2B24] ml-1">{{ $userExistingReview->rating }}/5</span>
                                    </span>
                                </div>
                                @if ($userExistingReview->comment)
                                    <p class="mt-2 text-sm text-[#5A6B61]">"{{ $userExistingReview->comment }}"</p>
                                @endif
                            @else
                                <h3 class="text-sm font-bold text-[#1E2B24]">Beri Ulasan sebagai Pembeli Terverifikasi</h3>
                                <p class="mt-1 text-xs text-[#5A6B61]">Anda telah membeli produk ini (Pesanan #{{ $userEligibleOrder->order_number }}). Berikan penilaian bintang dan pengalaman Anda:</p>
                                <form action="{{ route('reviews.store') }}" method="POST" class="mt-3 space-y-3">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="order_id" value="{{ $userEligibleOrder->id }}">

                                    <div>
                                        <label class="block text-xs font-bold text-[#1E2B24] mb-1">Pilih Rating Bintang</label>
                                        <div class="flex items-center gap-2">
                                            @for ($r = 5; $r >= 1; $r--)
                                                <label class="flex items-center gap-1 cursor-pointer text-xs font-semibold rounded-lg border border-[#D5E0D8] bg-white px-2.5 py-1.5 hover:border-[#4F7A63]">
                                                    <input type="radio" name="rating" value="{{ $r }}" {{ $r === 5 ? 'checked' : '' }} class="text-[#4F7A63] focus:ring-[#4F7A63]">
                                                    <span class="text-amber-500">★</span> {{ $r }}
                                                </label>
                                            @endfor
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-[#1E2B24] mb-1">Komentar / Pengalaman (Opsional)</label>
                                        <textarea name="comment" rows="2" placeholder="Bagikan ulasan Anda tentang kualitas produk..."
                                            class="w-full rounded-xl border border-[#D5E0D8] p-2.5 text-xs text-[#1E2B24] outline-none focus:border-[#4F7A63]"></textarea>
                                    </div>

                                    <button type="submit" class="rounded-xl bg-[#4F7A63] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#3E6350]">
                                        Kirim Ulasan & Bintang
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    @forelse ($product->reviews->take(5) as $review)
                        <div class="mt-5 border-t border-[#D5E0D8] pt-5 first:border-0 first:pt-0">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 place-items-center rounded-full bg-[#2F4F3E] text-sm font-bold text-white">
                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-[#1E2B24]">{{ $review->user->name ?? 'Pengguna' }}</p>
                                    <span class="flex items-center gap-0.5 text-amber-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <x-icon :name="$i <= $review->rating ? 'star' : 'star-outline'" class="h-3.5 w-3.5" />
                                        @endfor
                                    </span>
                                </div>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-[#5A6B61]">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="mt-4 rounded-2xl bg-[#F6F9F7] px-5 py-8 text-center text-sm text-[#5A6B61]">
                            Belum ada ulasan untuk produk ini. Ulasan bintang hanya dapat diberikan oleh pembeli yang telah membeli produk ini.
                        </p>
                    @endforelse
                </div>
            </div>

            <aside class="lg:sticky lg:top-32 lg:self-start">
                <h2 class="text-lg font-extrabold text-[#1E2B24]">Produk yang Direkomendasikan</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($related as $item)
                        <a href="{{ route('products.show', $item->slug) }}"
                            class="flex gap-4 rounded-2xl border border-[#D5E0D8] bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg hover:border-[#4F7A63]">
                            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="h-20 w-20 shrink-0 rounded-xl object-cover">
                            <div class="min-w-0">
                                <p class="line-clamp-2 text-sm font-semibold text-[#1E2B24]">{{ $item->name }}</p>
                                @if ($item->rating > 0)
                                    <span class="mt-1 flex items-center gap-1 text-xs text-amber-400">
                                        <x-icon name="star" class="h-3.5 w-3.5" />
                                        <span class="font-medium text-[#5A6B61]">{{ number_format($item->rating, 1) }}</span>
                                    </span>
                                @else
                                    <span class="mt-1 block text-xs text-[#5A6B61]">{{ $item->sold > 0 ? number_format($item->sold, 0, ',', '.') . ' terjual' : '0 terjual' }}</span>
                                @endif
                                <p class="mt-1 text-sm font-extrabold text-[#4F7A63]">{{ $item->price_label }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </aside>
        </div>
    </div>

    @if ($product->hasVariants())
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const variants = @json($product->variants);
                    const hasSizes = {{ !empty($product->sizes) ? 'true' : 'false' }};
                    const hasColors = {{ !empty($product->colors) ? 'true' : 'false' }};
                    const stockStatus = document.getElementById('product-stock-status');
                    const qtyInput = document.getElementById('product-quantity-input');
                    const btnCart = document.getElementById('btn-add-cart');
                    const btnBuy = document.getElementById('btn-buy-now');
                    const priceDisplay = document.getElementById('product-price-display');
                    const basePriceFormatted = @json($product->price_label);

                    function getSelectedSize() {
                        const checked = document.querySelector('input[name="size"]:checked');
                        return checked ? checked.value : null;
                    }

                    function getSelectedColor() {
                        const checked = document.querySelector('input[name="color"]:checked');
                        return checked ? checked.value : null;
                    }

                    function updateVariantState() {
                        const selectedSize = getSelectedSize();
                        const selectedColor = getSelectedColor();

                        if ((hasSizes && !selectedSize) || (hasColors && !selectedColor)) {
                            if (stockStatus) stockStatus.innerHTML = '<span class="text-[#5A6B61]">Pilih varian untuk melihat ketersediaan</span>';
                            return;
                        }

                        const match = variants.find(v => {
                            const sizeMatch = !hasSizes || (v.size && v.size.toLowerCase() === selectedSize?.toLowerCase());
                            const colorMatch = !hasColors || (v.color && v.color.toLowerCase() === selectedColor?.toLowerCase());
                            return sizeMatch && colorMatch;
                        });

                        if (!match) {
                            if (stockStatus) stockStatus.innerHTML = '<span class="text-[#E53935] font-semibold">Kombinasi varian ini tidak tersedia</span>';
                            if (btnCart) btnCart.disabled = true;
                            if (btnBuy) btnBuy.disabled = true;
                            if (qtyInput) {
                                qtyInput.max = 0;
                                qtyInput.value = 0;
                            }
                            return;
                        }

                        const stock = parseInt(match.stock, 10);

                        if (stock > 0) {
                            if (stockStatus) stockStatus.innerHTML = `<span class="text-emerald-700 font-medium">Tersisa ${stock} unit</span>`;
                            if (btnCart) btnCart.disabled = false;
                            if (btnBuy) btnBuy.disabled = false;
                            if (qtyInput) {
                                qtyInput.max = stock;
                                if (parseInt(qtyInput.value, 10) > stock) {
                                    qtyInput.value = stock;
                                }
                                if (parseInt(qtyInput.value, 10) < 1) {
                                    qtyInput.value = 1;
                                }
                            }
                        } else {
                            if (stockStatus) stockStatus.innerHTML = '<span class="text-[#E53935] font-bold">Stok varian ini habis</span>';
                            if (btnCart) btnCart.disabled = true;
                            if (btnBuy) btnBuy.disabled = true;
                            if (qtyInput) {
                                qtyInput.max = 0;
                                qtyInput.value = 0;
                            }
                        }

                        if (priceDisplay && match.price) {
                            priceDisplay.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(match.price);
                        } else if (priceDisplay) {
                            priceDisplay.textContent = basePriceFormatted;
                        }
                    }

                    document.querySelectorAll('input[name="size"], input[name="color"]').forEach(input => {
                        input.addEventListener('change', updateVariantState);
                    });

                    updateVariantState();
                });
            </script>
        @endpush
    @endif
@endsection
