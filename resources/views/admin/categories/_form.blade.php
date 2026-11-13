@php
    $category = $category ?? null;
@endphp

<div class="max-w-2xl rounded-3xl border border-[#8CC4FF] bg-white p-6 shadow-sm">
    <h2 class="text-lg font-extrabold text-[#0A2A5E]">Informasi Kategori</h2>
    <div class="mt-5 space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-[#10213F]">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                    class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
            </div>
            <div>
                <label class="text-sm font-semibold text-[#10213F]">Ikon (emoji)</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}" placeholder="⚽"
                    class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
            </div>
        </div>
        <div>
            <label class="text-sm font-semibold text-[#10213F]">Deskripsi</label>
            <textarea name="description" rows="3"
                class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">{{ old('description', $category->description ?? '') }}</textarea>
        </div>
        <div>
            <label class="text-sm font-semibold text-[#10213F]">URL Gambar</label>
            <input type="url" name="image" value="{{ old('image', $category->image ?? '') }}" placeholder="https://..."
                class="mt-1.5 w-full rounded-xl border border-[#8CC4FF]/60 bg-[#F4F8FF] px-4 py-3 text-sm outline-none focus:border-[#0A6CFF] focus:bg-white">
            <p class="mt-1 text-xs text-neutral-400">Kosongkan untuk memakai gambar otomatis.</p>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-xl bg-[#0A6CFF] px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3D9BFF]">
            {{ $submitLabel ?? 'Simpan Kategori' }}
        </button>
        <a href="{{ route('admin.categories.index') }}" class="grid place-items-center rounded-xl border border-[#8CC4FF] px-6 text-sm font-bold text-[#0A6CFF] transition hover:bg-[#E6F1FF]">
            Batal
        </a>
    </div>
</div>
