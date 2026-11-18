@php
    $product = $product ?? null;
    $sizePresets = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'All Size', '38', '39', '40', '41', '42', '43', '44'];
    $colorPresets = ['Hitam', 'Putih', 'Navy', 'Merah', 'Biru', 'Oranye', 'Hijau', 'Abu-abu', 'Kuning'];
    
    $initialVariants = old('variants');
    if ($initialVariants === null && $product) {
        $initialVariants = $product->variants->map(fn ($v) => [
            'size' => $v->size,
            'color' => $v->color,
            'stock' => $v->stock,
            'price' => $v->price,
        ])->toArray();
    }
    $initialVariants = $initialVariants ?? [];
    $hasVariantsInitial = old('has_variants', ! empty($initialVariants));
@endphp

<div class="grid gap-6 lg:grid-cols-[1.6fr_1fr]" data-product-form>
    <div class="space-y-6">
        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Informasi Produk</h2>
            <div class="mt-5 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Kategori</label>
                    <select name="category_id" required class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Deskripsi</label>
                    <textarea name="description" rows="5" required
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Spesifikasi (opsional)</label>
                    <textarea name="specification" rows="3"
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">{{ old('specification', $product->specification ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Kelola Varian & Stok per Varian --}}
        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold text-[#1E2B24]">Varian &amp; Stok Varian</h2>
                    <p class="text-xs text-[#5A6B61]">Aktifkan jika produk memiliki opsi ukuran, warna, dsb. dengan stok masing-masing.</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-[#1E2B24]">
                    <input type="checkbox" name="has_variants" value="1" id="toggle-has-variants" @checked($hasVariantsInitial)
                        class="h-4 w-4 rounded border-[#D5E0D8] text-[#4F7A63] focus:ring-[#4F7A63]">
                    <span>Gunakan Varian</span>
                </label>
            </div>

            <div id="variants-container" class="{{ $hasVariantsInitial ? '' : 'hidden' }} mt-6 space-y-6">
                {{-- Pembuat Kombinasi Otomatis --}}
                <div class="rounded-2xl border border-[#D5E0D8] bg-[#F6F9F7] p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#4F7A63]">⚡ Pembuat Kombinasi Cepat</p>
                    <p class="mt-1 text-xs text-[#5A6B61]">Pilih opsi ukuran dan warna di bawah ini, lalu klik tombol untuk membuat tabel kombinasi varian.</p>

                    <div class="mt-4 space-y-3">
                        <div>
                            <span class="text-xs font-semibold text-[#1E2B24]">Pilih Ukuran:</span>
                            <div class="mt-2 flex flex-wrap gap-1.5" id="preset-size-chips">
                                @foreach ($sizePresets as $sz)
                                    <button type="button" data-chip="size" data-val="{{ $sz }}"
                                        class="rounded-lg border border-[#D5E0D8] bg-white px-2.5 py-1 text-xs font-semibold text-[#1E2B24] transition hover:border-[#4F7A63]">
                                        {{ $sz }}
                                    </button>
                                @endforeach
                            </div>
                            <div class="mt-2 flex max-w-xs gap-2">
                                <input type="text" id="custom-size-input" placeholder="Ukuran lain..."
                                    class="w-full rounded-lg border border-[#D5E0D8] bg-white px-3 py-1.5 text-xs text-[#1E2B24] outline-none focus:border-[#4F7A63]">
                                <button type="button" id="btn-add-custom-size"
                                    class="shrink-0 rounded-lg bg-[#2F4F3E] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-[#243F31]">
                                    + Tambah
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-[#D5E0D8] pt-3">
                            <span class="text-xs font-semibold text-[#1E2B24]">Pilih Warna:</span>
                            <div class="mt-2 flex flex-wrap gap-1.5" id="preset-color-chips">
                                @foreach ($colorPresets as $cl)
                                    <button type="button" data-chip="color" data-val="{{ $cl }}"
                                        class="rounded-lg border border-[#D5E0D8] bg-white px-2.5 py-1 text-xs font-semibold text-[#1E2B24] transition hover:border-[#4F7A63]">
                                        {{ $cl }}
                                    </button>
                                @endforeach
                            </div>
                            <div class="mt-2 flex max-w-xs gap-2">
                                <input type="text" id="custom-color-input" placeholder="Warna lain..."
                                    class="w-full rounded-lg border border-[#D5E0D8] bg-white px-3 py-1.5 text-xs text-[#1E2B24] outline-none focus:border-[#4F7A63]">
                                <button type="button" id="btn-add-custom-color"
                                    class="shrink-0 rounded-lg bg-[#2F4F3E] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-[#243F31]">
                                    + Tambah
                                </button>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="button" id="btn-generate-combinations"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#4F7A63] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                                <x-icon name="sparkles" class="h-4 w-4" /> Buat Kombinasi Varian
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tabel Daftar Varian & Stok --}}
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-[#1E2B24]">Daftar Varian</h3>
                            <span id="variant-count-badge" class="rounded-full bg-[#4F7A63]/10 px-2.5 py-0.5 text-xs font-bold text-[#4F7A63]">
                                0 Varian
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="number" id="bulk-stock-input" placeholder="Stok seragam..." min="0"
                                class="w-28 rounded-lg border border-[#D5E0D8] bg-[#F6F9F7] px-2.5 py-1.5 text-xs text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                            <button type="button" id="btn-apply-bulk-stock"
                                class="rounded-lg border border-[#D5E0D8] bg-white px-3 py-1.5 text-xs font-semibold text-[#1E2B24] transition hover:bg-[#F6F9F7]">
                                Terapkan ke Semua
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-[#D5E0D8]">
                        <table class="w-full min-w-[500px] text-sm">
                            <thead>
                                <tr class="border-b border-[#D5E0D8] bg-[#F6F9F7] text-left text-xs uppercase tracking-wide text-[#5A6B61]">
                                    <th class="py-2.5 pl-4 pr-2 font-semibold">Ukuran</th>
                                    <th class="py-2.5 px-2 font-semibold">Warna</th>
                                    <th class="py-2.5 px-2 font-semibold">Stok <span class="text-[#E53935]">*</span></th>
                                    <th class="py-2.5 px-2 font-semibold">Harga Khusus (Rp)</th>
                                    <th class="py-2.5 pl-2 pr-4 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="variants-table-body" class="divide-y divide-[#D5E0D8] bg-white">
                                {{-- Baris varian dimasukkan oleh JavaScript --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                        <button type="button" id="btn-add-manual-variant"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-[#D5E0D8] bg-white px-3.5 py-2 text-xs font-semibold text-[#1E2B24] transition hover:border-[#4F7A63] hover:text-[#4F7A63]">
                            <x-icon name="plus" class="h-4 w-4" /> Tambah Baris Manual
                        </button>
                        <button type="button" id="btn-clear-all-variants"
                            class="text-xs font-semibold text-[#E53935] hover:underline">
                            Hapus Semua Varian
                        </button>
                    </div>

                    <div class="flex items-center justify-between rounded-xl bg-[#DCE8DF]/40 p-4">
                        <span class="text-xs font-semibold text-[#1E2B24]">Total Stok Akumulasi Varian:</span>
                        <span id="variant-total-stock-display" class="text-lg font-black text-[#4F7A63]">0 unit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Harga &amp; Stok</h2>
            <div class="mt-5 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Harga Utama (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" min="0" required
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Harga Sebelum Diskon (opsional)</label>
                    <input type="number" name="compare_price" value="{{ old('compare_price', $product->compare_price ?? '') }}" min="0"
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">
                        Total Stok Produk
                        <span id="stock-help-text" class="text-xs font-normal text-[#5A6B61]"></span>
                    </label>
                    <input type="number" name="stock" id="main-stock-input" value="{{ old('stock', $product->stock ?? 0) }}" min="0"
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Label / Badge</label>
                    <select name="badge" class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                        <option value="">Tanpa Label</option>
                        <option value="baru" @selected(old('badge', $product->badge ?? '') === 'baru')>Baru</option>
                        <option value="terlaris" @selected(old('badge', $product->badge ?? '') === 'terlaris')>Terlaris</option>
                        <option value="promo" @selected(old('badge', $product->badge ?? '') === 'promo')>Promo</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-[#1E2B24]">Gambar &amp; Pengaturan</h2>
            <div class="mt-5 space-y-4">
                @if (!empty($product?->image))
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#5A6B61]">Gambar Saat Ini</p>
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-xl border border-[#D5E0D8] object-cover">
                            <p class="text-xs text-[#5A6B61]">Unggah berkas baru atau masukkan URL di bawah untuk mengganti gambar.</p>
                        </div>
                    </div>
                @endif
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Unggah Berkas Gambar</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml"
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-2.5 text-sm text-[#1E2B24] outline-none file:mr-4 file:rounded-lg file:border-0 file:bg-[#4F7A63] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-[#3E6350] focus:border-[#4F7A63] focus:bg-white">
                    <p class="mt-1 text-xs text-[#5A6B61]">Format: JPG, PNG, WEBP, atau SVG (maks. 4MB).</p>
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#1E2B24]">Atau URL Gambar (opsional)</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://..."
                        class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    <p class="mt-1 text-xs text-[#5A6B61]">Kosongkan jika mengunggah berkas atau menggunakan gambar bawaan.</p>
                </div>
                <label class="flex items-center gap-3 text-sm text-[#1E2B24]">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false)) class="h-4 w-4 rounded border-[#D5E0D8] text-[#4F7A63] focus:ring-[#4F7A63]">
                    Tampilkan sebagai produk unggulan
                </label>
                <label class="flex items-center gap-3 text-sm text-[#1E2B24]">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="h-4 w-4 rounded border-[#D5E0D8] text-[#4F7A63] focus:ring-[#4F7A63]">
                    Produk aktif (tampil di toko)
                </label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
                {{ $submitLabel ?? 'Simpan Produk' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="grid place-items-center rounded-xl border border-[#D5E0D8] px-6 text-sm font-bold text-[#1E2B24] transition hover:bg-[#F6F9F7]">
                Batal
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('toggle-has-variants');
    const container = document.getElementById('variants-container');
    const tableBody = document.getElementById('variants-table-body');
    const mainStockInput = document.getElementById('main-stock-input');
    const stockHelpText = document.getElementById('stock-help-text');
    const totalDisplay = document.getElementById('variant-total-stock-display');
    const countBadge = document.getElementById('variant-count-badge');

    const selectedSizes = new Set();
    const selectedColors = new Set();

    let initialVariants = @json($initialVariants);

    function updateStockMode() {
        const hasVariants = toggle.checked;
        if (hasVariants) {
            container.classList.remove('hidden');
            mainStockInput.setAttribute('readonly', 'true');
            mainStockInput.classList.add('bg-neutral-100', 'cursor-not-allowed');
            stockHelpText.textContent = '(Otomatis dihitung dari varian)';
            recalcTotalStock();
        } else {
            container.classList.add('hidden');
            mainStockInput.removeAttribute('readonly');
            mainStockInput.classList.remove('bg-neutral-100', 'cursor-not-allowed');
            stockHelpText.textContent = '';
        }
    }

    function recalcTotalStock() {
        if (!toggle.checked) return;
        const stockInputs = tableBody.querySelectorAll('input[data-field="stock"]');
        let total = 0;
        stockInputs.forEach(input => {
            const val = parseInt(input.value, 10);
            if (!isNaN(val) && val >= 0) total += val;
        });
        totalDisplay.textContent = total + ' unit';
        mainStockInput.value = total;
        countBadge.textContent = stockInputs.length + ' Varian';
    }

    function createVariantRow(variant = {}, index = null) {
        if (index === null) {
            index = tableBody.querySelectorAll('tr').length;
        }

        const sizeVal = variant.size ?? '';
        const colorVal = variant.color ?? '';
        const stockVal = variant.stock ?? 0;
        const priceVal = variant.price ?? '';

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-[#F6F9F7] transition';
        tr.innerHTML = `
            <td class="py-2.5 pl-4 pr-2">
                <input type="text" name="variants[${index}][size]" value="${escapeHtml(sizeVal)}" placeholder="Mis. M / 42"
                    class="w-full rounded-lg border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-1.5 text-xs text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
            </td>
            <td class="py-2.5 px-2">
                <input type="text" name="variants[${index}][color]" value="${escapeHtml(colorVal)}" placeholder="Mis. Hitam / Biru"
                    class="w-full rounded-lg border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-1.5 text-xs text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
            </td>
            <td class="py-2.5 px-2">
                <input type="number" name="variants[${index}][stock]" value="${escapeHtml(stockVal)}" min="0" required data-field="stock"
                    class="w-24 rounded-lg border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-1.5 text-xs font-bold text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
            </td>
            <td class="py-2.5 px-2">
                <input type="number" name="variants[${index}][price]" value="${escapeHtml(priceVal)}" min="0" placeholder="Default"
                    class="w-32 rounded-lg border border-[#D5E0D8] bg-[#F6F9F7] px-3 py-1.5 text-xs text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
            </td>
            <td class="py-2.5 pl-2 pr-4 text-right">
                <button type="button" class="btn-remove-row text-xs font-bold text-[#E53935] hover:underline">Hapus</button>
            </td>
        `;

        tr.querySelector('.btn-remove-row').addEventListener('click', function () {
            tr.remove();
            reindexRows();
            recalcTotalStock();
        });

        tr.querySelector('input[data-field="stock"]').addEventListener('input', recalcTotalStock);

        return tr;
    }

    function reindexRows() {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach((row, i) => {
            row.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/variants\[\d+\]/, `variants[${i}]`));
                }
            });
        });
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return String(text).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // Load initial variants
    if (initialVariants && initialVariants.length > 0) {
        initialVariants.forEach((v, i) => {
            tableBody.appendChild(createVariantRow(v, i));
        });
    }

    toggle.addEventListener('change', updateStockMode);
    updateStockMode();

    // Chip selections
    document.querySelectorAll('[data-chip="size"]').forEach(btn => {
        btn.addEventListener('click', function () {
            const val = this.dataset.val;
            if (selectedSizes.has(val)) {
                selectedSizes.delete(val);
                this.classList.remove('bg-[#4F7A63]', 'text-white', 'border-[#4F7A63]');
                this.classList.add('bg-white', 'text-[#1E2B24]', 'border-[#D5E0D8]');
            } else {
                selectedSizes.add(val);
                this.classList.add('bg-[#4F7A63]', 'text-white', 'border-[#4F7A63]');
                this.classList.remove('bg-white', 'text-[#1E2B24]', 'border-[#D5E0D8]');
            }
        });
    });

    document.querySelectorAll('[data-chip="color"]').forEach(btn => {
        btn.addEventListener('click', function () {
            const val = this.dataset.val;
            if (selectedColors.has(val)) {
                selectedColors.delete(val);
                this.classList.remove('bg-[#4F7A63]', 'text-white', 'border-[#4F7A63]');
                this.classList.add('bg-white', 'text-[#1E2B24]', 'border-[#D5E0D8]');
            } else {
                selectedColors.add(val);
                this.classList.add('bg-[#4F7A63]', 'text-white', 'border-[#4F7A63]');
                this.classList.remove('bg-white', 'text-[#1E2B24]', 'border-[#D5E0D8]');
            }
        });
    });

    // Custom size/color
    document.getElementById('btn-add-custom-size').addEventListener('click', function () {
        const inp = document.getElementById('custom-size-input');
        const val = inp.value.trim();
        if (val) {
            selectedSizes.add(val);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'rounded-lg border border-[#4F7A63] bg-[#4F7A63] px-2.5 py-1 text-xs font-semibold text-white transition';
            btn.textContent = val;
            btn.addEventListener('click', function() {
                selectedSizes.delete(val);
                btn.remove();
            });
            document.getElementById('preset-size-chips').appendChild(btn);
            inp.value = '';
        }
    });

    document.getElementById('btn-add-custom-color').addEventListener('click', function () {
        const inp = document.getElementById('custom-color-input');
        const val = inp.value.trim();
        if (val) {
            selectedColors.add(val);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'rounded-lg border border-[#4F7A63] bg-[#4F7A63] px-2.5 py-1 text-xs font-semibold text-white transition';
            btn.textContent = val;
            btn.addEventListener('click', function() {
                selectedColors.delete(val);
                btn.remove();
            });
            document.getElementById('preset-color-chips').appendChild(btn);
            inp.value = '';
        }
    });

    // Generate combinations
    document.getElementById('btn-generate-combinations').addEventListener('click', function () {
        const sizes = Array.from(selectedSizes);
        const colors = Array.from(selectedColors);

        if (sizes.length === 0 && colors.length === 0) {
            alert('Pilih minimal satu ukuran atau satu warna terlebih dahulu.');
            return;
        }

        const newVariants = [];
        if (sizes.length > 0 && colors.length > 0) {
            sizes.forEach(s => {
                colors.forEach(c => {
                    newVariants.push({ size: s, color: c, stock: 10, price: '' });
                });
            });
        } else if (sizes.length > 0) {
            sizes.forEach(s => {
                newVariants.push({ size: s, color: '', stock: 10, price: '' });
            });
        } else {
            colors.forEach(c => {
                newVariants.push({ size: '', color: c, stock: 10, price: '' });
            });
        }

        tableBody.innerHTML = '';
        newVariants.forEach((v, i) => {
            tableBody.appendChild(createVariantRow(v, i));
        });

        recalcTotalStock();
    });

    // Manual add row
    document.getElementById('btn-add-manual-variant').addEventListener('click', function () {
        tableBody.appendChild(createVariantRow({ size: '', color: '', stock: 5, price: '' }));
        recalcTotalStock();
    });

    // Bulk stock
    document.getElementById('btn-apply-bulk-stock').addEventListener('click', function () {
        const val = parseInt(document.getElementById('bulk-stock-input').value, 10);
        if (isNaN(val) || val < 0) {
            alert('Masukkan angka stok yang valid.');
            return;
        }
        tableBody.querySelectorAll('input[data-field="stock"]').forEach(inp => {
            inp.value = val;
        });
        recalcTotalStock();
    });

    // Clear all
    document.getElementById('btn-clear-all-variants').addEventListener('click', function () {
        if (confirm('Kosongkan semua daftar varian?')) {
            tableBody.innerHTML = '';
            recalcTotalStock();
        }
    });
});
</script>
@endpush
