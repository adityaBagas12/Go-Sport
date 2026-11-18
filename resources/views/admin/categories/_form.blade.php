@php
    $category = $category ?? null;
@endphp

<div class="max-w-2xl rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
    <h2 class="text-lg font-extrabold text-[#1E2B24]">Informasi Kategori</h2>
    <div class="mt-5 space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-[#1E2B24]">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                    class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
            </div>
            <div>
                <label class="text-sm font-semibold text-[#1E2B24]">Ikon (emoji)</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}" placeholder="⚽"
                    class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
            </div>
        </div>
        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">Deskripsi</label>
            <textarea name="description" rows="3"
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">{{ old('description', $category->description ?? '') }}</textarea>
        </div>
        <div>
            <label class="text-sm font-semibold text-[#1E2B24]">URL Gambar</label>
            <input type="url" name="image" value="{{ old('image', $category->image ?? '') }}" placeholder="https://..."
                class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
            <p class="mt-1 text-xs text-[#5A6B61]">Kosongkan untuk memakai gambar otomatis.</p>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
            {{ $submitLabel ?? 'Simpan Kategori' }}
        </button>
        <a href="{{ route('admin.categories.index') }}" class="grid place-items-center rounded-xl border border-[#D5E0D8] px-6 text-sm font-bold text-[#1E2B24] transition hover:bg-[#F6F9F7]">
            Batal
        </a>
    </div>
</div>
