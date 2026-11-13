@php
    $category = $category ?? null;
@endphp

<div class="max-w-2xl rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-extrabold text-neutral-900">Informasi Kategori</h2>
    <div class="mt-5 space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-neutral-700">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                    class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
            </div>
            <div>
                <label class="text-sm font-semibold text-neutral-700">Ikon (emoji)</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}" placeholder="⚽"
                    class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
            </div>
        </div>
        <div>
            <label class="text-sm font-semibold text-neutral-700">Deskripsi</label>
            <textarea name="description" rows="3"
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">{{ old('description', $category->description ?? '') }}</textarea>
        </div>
        <div>
            <label class="text-sm font-semibold text-neutral-700">URL Gambar</label>
            <input type="url" name="image" value="{{ old('image', $category->image ?? '') }}" placeholder="https://..."
                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
            <p class="mt-1 text-xs text-neutral-400">Kosongkan untuk memakai gambar otomatis.</p>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-brand-600">
            {{ $submitLabel ?? 'Simpan Kategori' }}
        </button>
        <a href="{{ route('admin.categories.index') }}" class="grid place-items-center rounded-xl border border-neutral-200 px-6 text-sm font-semibold text-neutral-600 hover:bg-neutral-100">
            Batal
        </a>
    </div>
</div>
