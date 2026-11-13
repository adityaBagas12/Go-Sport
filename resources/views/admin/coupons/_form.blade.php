@php
    $coupon = $coupon ?? null;
@endphp

<div class="max-w-2xl rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-extrabold text-neutral-900">Informasi Voucher</h2>
    <div class="mt-5 space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-neutral-700">Kode Voucher</label>
                <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required placeholder="GOSPORT10"
                    class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm uppercase outline-none focus:border-brand-500 focus:bg-white">
            </div>
            <div>
                <label class="text-sm font-semibold text-neutral-700">Tipe Diskon</label>
                <select name="type" required class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                    <option value="percent" @selected(old('type', $coupon->type ?? 'percent') === 'percent')>Persentase (%)</option>
                    <option value="fixed" @selected(old('type', $coupon->type ?? '') === 'fixed')>Nominal (Rp)</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-neutral-700">Nilai Diskon</label>
                <input type="number" name="value" value="{{ old('value', $coupon->value ?? '') }}" min="1" required
                    class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                <p class="mt-1 text-xs text-neutral-400">Isi 10 untuk 10%, atau 50000 untuk potongan Rp 50.000.</p>
            </div>
            <div>
                <label class="text-sm font-semibold text-neutral-700">Minimal Belanja (Rp)</label>
                <input type="number" name="min_order" value="{{ old('min_order', $coupon->min_order ?? 0) }}" min="0" required
                    class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
            </div>
        </div>
        <label class="flex items-center gap-3 text-sm text-neutral-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="h-4 w-4 rounded border-neutral-300 text-brand-500 focus:ring-brand-500">
            Voucher aktif
        </label>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-brand-600">
            {{ $submitLabel ?? 'Simpan Voucher' }}
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="grid place-items-center rounded-xl border border-neutral-200 px-6 text-sm font-semibold text-neutral-600 hover:bg-neutral-100">
            Batal
        </a>
    </div>
</div>
