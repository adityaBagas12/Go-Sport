@php
    $coupon = $coupon ?? null;
@endphp

<div class="max-w-2xl rounded-3xl border border-[#D5E0D8] bg-white p-6 shadow-sm">
    <h2 class="text-lg font-extrabold text-[#1E2B24]">Informasi Voucher</h2>
    <div class="mt-5 space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-[#1E2B24]">Kode Voucher</label>
                <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required placeholder="GOSPORT10"
                    class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] uppercase outline-none focus:border-[#4F7A63] focus:bg-white">
            </div>
            <div>
                <label class="text-sm font-semibold text-[#1E2B24]">Tipe Diskon</label>
                <select name="type" required class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                    <option value="percent" @selected(old('type', $coupon->type ?? 'percent') === 'percent')>Persentase (%)</option>
                    <option value="fixed" @selected(old('type', $coupon->type ?? '') === 'fixed')>Nominal (Rp)</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-[#1E2B24]">Nilai Diskon</label>
                <input type="number" name="value" value="{{ old('value', $coupon->value ?? '') }}" min="1" required
                    class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
                <p class="mt-1 text-xs text-[#5A6B61]">Isi 10 untuk 10%, atau 50000 untuk potongan Rp 50.000.</p>
            </div>
            <div>
                <label class="text-sm font-semibold text-[#1E2B24]">Minimal Belanja (Rp)</label>
                <input type="number" name="min_order" value="{{ old('min_order', $coupon->min_order ?? 0) }}" min="0" required
                    class="mt-1.5 w-full rounded-xl border border-[#D5E0D8] bg-[#F6F9F7] px-4 py-3 text-sm text-[#1E2B24] outline-none focus:border-[#4F7A63] focus:bg-white">
            </div>
        </div>
        <label class="flex items-center gap-3 text-sm text-[#1E2B24]">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="h-4 w-4 rounded border-[#D5E0D8] text-[#4F7A63] focus:ring-[#4F7A63]">
            Voucher aktif
        </label>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-xl bg-[#4F7A63] px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#3E6350]">
            {{ $submitLabel ?? 'Simpan Voucher' }}
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="grid place-items-center rounded-xl border border-[#D5E0D8] px-6 text-sm font-bold text-[#1E2B24] transition hover:bg-[#F6F9F7]">
            Batal
        </a>
    </div>
</div>
