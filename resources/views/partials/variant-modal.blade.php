<div id="quick-variant-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop with blur -->
    <div id="quick-variant-backdrop" class="fixed inset-0 bg-[#1E2B24]/60 backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative w-full transform overflow-hidden rounded-3xl border border-[#D5E0D8] bg-white text-left shadow-2xl transition-all sm:my-8 sm:max-w-md">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-[#D5E0D8] px-6 py-4">
                <h3 class="text-base font-extrabold text-[#1E2B24]" id="modal-title">Pilih Varian Produk</h3>
                <button type="button" id="quick-variant-close" class="grid h-8 w-8 place-items-center rounded-xl text-[#5A6B61] transition hover:bg-[#F6F9F7] hover:text-[#1E2B24]">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <!-- Modal Body with Form -->
            <form id="quick-variant-form" action="{{ route('cart.add') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="product_id" id="quick-modal-product-id">

                <!-- Product Preview Row -->
                <div class="flex gap-4">
                    <img id="quick-modal-image" src="" alt="" class="h-20 w-20 shrink-0 rounded-2xl border border-[#D5E0D8] object-cover">
                    <div class="min-w-0 flex-1">
                        <h4 id="quick-modal-name" class="line-clamp-2 text-sm font-bold text-[#1E2B24]"></h4>
                        <div class="mt-1 flex items-baseline gap-2">
                            <span id="quick-modal-price" class="text-lg font-black text-[#4F7A63]"></span>
                        </div>
                        <p id="quick-modal-stock" class="mt-1 text-xs text-[#5A6B61]"></p>
                    </div>
                </div>

                <!-- Sizes Section -->
                <div id="quick-modal-sizes-group" class="mt-5 border-t border-[#D5E0D8] pt-4">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold uppercase tracking-wider text-[#1E2B24]">Pilih Ukuran</label>
                        <span class="text-[11px] text-[#5A6B61]">Wajib dipilih</span>
                    </div>
                    <div id="quick-modal-sizes-container" class="mt-2.5 flex flex-wrap gap-2"></div>
                </div>

                <!-- Colors Section -->
                <div id="quick-modal-colors-group" class="mt-4 border-t border-[#D5E0D8] pt-4">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold uppercase tracking-wider text-[#1E2B24]">Pilih Warna</label>
                        <span class="text-[11px] text-[#5A6B61]">Wajib dipilih</span>
                    </div>
                    <div id="quick-modal-colors-container" class="mt-2.5 flex flex-wrap gap-2"></div>
                </div>

                <!-- Quantity Section -->
                <div class="mt-4 border-t border-[#D5E0D8] pt-4">
                    <label class="text-xs font-bold uppercase tracking-wider text-[#1E2B24]">Jumlah</label>
                    <div class="mt-2.5 flex items-center gap-4">
                        <div data-qty class="inline-flex items-center rounded-xl border border-[#D5E0D8] bg-white">
                            <button type="button" data-qty-decrease class="grid h-10 w-10 place-items-center text-[#5A6B61] hover:text-[#4F7A63]">
                                <x-icon name="minus" class="h-4 w-4" />
                            </button>
                            <input type="number" id="quick-modal-quantity" name="quantity" value="1" min="1" max="99"
                                class="h-10 w-14 border-x border-[#D5E0D8] text-center text-sm font-bold text-[#1E2B24] outline-none">
                            <button type="button" data-qty-increase class="grid h-10 w-10 place-items-center text-[#5A6B61] hover:text-[#4F7A63]">
                                <x-icon name="plus" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" id="quick-modal-submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#4F7A63] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-[#4F7A63]/30 transition hover:bg-[#3E6350] disabled:cursor-not-allowed disabled:bg-neutral-300 disabled:shadow-none">
                        <x-icon name="cart" class="h-5 w-5" /> Tambah ke Keranjang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
