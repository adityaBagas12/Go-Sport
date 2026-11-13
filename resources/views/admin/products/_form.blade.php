@php
    $product = $product ?? null;
    $sizeOptions = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'All Size'];
    $colorOptions = ['Hitam', 'Putih', 'Navy', 'Merah', 'Biru', 'Oranye', 'Hijau', 'Abu-abu', 'Kuning', 'Pink'];
    $selectedSizes = old('sizes', $product->sizes ?? []);
    $selectedColors = old('colors', $product->colors ?? []);
@endphp

<div class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
    <div class="space-y-6">
        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-neutral-900">Informasi Produk</h2>
            <div class="mt-5 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Kategori</label>
                    <select name="category_id" required class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Deskripsi</label>
                    <textarea name="description" rows="5" required
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Spesifikasi (opsional)</label>
                    <textarea name="specification" rows="3"
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">{{ old('specification', $product->specification ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-neutral-900">Varian</h2>
            <div class="mt-5 space-y-5">
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Pilihan Ukuran</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($sizeOptions as $size)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="{{ $size }}" @checked(in_array($size, $selectedSizes)) class="peer sr-only">
                                <span class="inline-block rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-600 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-600">
                                    {{ $size }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Pilihan Warna</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($colorOptions as $color)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="colors[]" value="{{ $color }}" @checked(in_array($color, $selectedColors)) class="peer sr-only">
                                <span class="inline-block rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-600 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-600">
                                    {{ $color }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-neutral-900">Harga & Stok</h2>
            <div class="mt-5 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" min="0" required
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Harga Sebelum Diskon (opsional)</label>
                    <input type="number" name="compare_price" value="{{ old('compare_price', $product->compare_price ?? '') }}" min="0"
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Label / Badge</label>
                    <select name="badge" class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                        <option value="">Tanpa Label</option>
                        <option value="baru" @selected(old('badge', $product->badge ?? '') === 'baru')>Baru</option>
                        <option value="terlaris" @selected(old('badge', $product->badge ?? '') === 'terlaris')>Terlaris</option>
                        <option value="promo" @selected(old('badge', $product->badge ?? '') === 'promo')>Promo</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-neutral-900">Gambar & Pengaturan</h2>
            <div class="mt-5 space-y-4">
                @if (!empty($product?->image))
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">Gambar Saat Ini</p>
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-xl border border-neutral-200 object-cover">
                            <p class="text-xs text-neutral-500">Unggah berkas baru atau masukkan URL di bawah untuk mengganti gambar.</p>
                        </div>
                    </div>
                @endif
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Unggah Berkas Gambar</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml"
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-2.5 text-sm outline-none file:mr-4 file:rounded-lg file:border-0 file:bg-brand-500 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-brand-600 focus:border-brand-500 focus:bg-white">
                    <p class="mt-1 text-xs text-neutral-400">Format: JPG, PNG, WEBP, atau SVG (maks. 4MB).</p>
                </div>
                <div>
                    <label class="text-sm font-semibold text-neutral-700">Atau URL Gambar (opsional)</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://..."
                        class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                    <p class="mt-1 text-xs text-neutral-400">Kosongkan jika mengunggah berkas atau menggunakan gambar bawaan.</p>
                </div>
                <label class="flex items-center gap-3 text-sm text-neutral-700">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false)) class="h-4 w-4 rounded border-neutral-300 text-brand-500 focus:ring-brand-500">
                    Tampilkan sebagai produk unggulan
                </label>
                <label class="flex items-center gap-3 text-sm text-neutral-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="h-4 w-4 rounded border-neutral-300 text-brand-500 focus:ring-brand-500">
                    Produk aktif (tampil di toko)
                </label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-brand-600">
                {{ $submitLabel ?? 'Simpan Produk' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="grid place-items-center rounded-xl border border-neutral-200 px-6 text-sm font-semibold text-neutral-600 hover:bg-neutral-100">
                Batal
            </a>
        </div>
    </div>
</div>
