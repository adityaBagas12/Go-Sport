@extends('layouts.app')

@section('title', 'Checkout - GoSport')

@section('content')
    <div class="border-b border-neutral-200 bg-white">
        <div class="container-page py-8">
            <nav class="flex items-center gap-2 text-sm text-neutral-500">
                <a href="{{ route('cart.index') }}" class="hover:text-brand-600">Keranjang</a>
                <x-icon name="chevron-right" class="h-4 w-4" />
                <span class="font-medium text-neutral-900">Checkout</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-neutral-900">Checkout</h1>
        </div>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
        @csrf
        @foreach ($items as $item)
            <input type="hidden" name="items[]" value="{{ $item->id }}">
        @endforeach
        <div class="container-page grid gap-8 py-10 lg:grid-cols-[1fr_400px]">
            <div class="space-y-6">
                @if ($addresses->count())
                    <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-extrabold text-neutral-900">Alamat Tersimpan</h2>
                        <p class="mt-1 text-sm text-neutral-500">Pilih alamat untuk mengisi form secara otomatis.</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ($addresses as $address)
                                <button type="button"
                                    class="rounded-2xl border border-neutral-200 p-4 text-left transition hover:border-brand-500 hover:bg-brand-50/40"
                                    data-address="{{ json_encode(['recipient_name' => $address->recipient_name, 'recipient_phone' => $address->phone, 'province' => $address->province, 'city' => $address->city, 'district' => $address->district, 'postal_code' => $address->postal_code, 'full_address' => $address->full_address]) }}">
                                    <p class="text-sm font-bold text-neutral-900">{{ $address->label }}</p>
                                    <p class="mt-1 text-xs text-neutral-500">{{ $address->recipient_name }} &middot; {{ $address->phone }}</p>
                                    <p class="mt-1 line-clamp-2 text-xs text-neutral-500">{{ $address->full_address }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                    <h2 class="flex items-center gap-2 text-lg font-extrabold text-neutral-900">
                        <x-icon name="map-pin" class="h-5 w-5 text-brand-500" /> Alamat Pengiriman
                    </h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-neutral-700">Nama Penerima</label>
                            <input type="text" name="recipient_name" id="field-recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" required
                                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-700">Nomor Telepon</label>
                            <input type="text" name="recipient_phone" id="field-recipient_phone" value="{{ old('recipient_phone', auth()->user()->phone) }}" required
                                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-700">Provinsi</label>
                            <select name="province" id="field-province" required
                                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                                <option value="">Pilih provinsi</option>
                                @foreach ($shippingRegions as $region)
                                    <optgroup label="{{ $region['name'] }}">
                                        @foreach ($region['provinces'] as $province)
                                            <option value="{{ $province }}" @selected(old('province') === $province)>{{ $province }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('province')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-700">Kota / Kabupaten</label>
                            <input type="text" name="city" id="field-city" value="{{ old('city') }}" required placeholder="Jakarta Pusat"
                                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-700">Kecamatan</label>
                            <input type="text" name="district" id="field-district" value="{{ old('district') }}" required placeholder="Menteng"
                                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-neutral-700">Kode Pos</label>
                            <input type="text" name="postal_code" id="field-postal_code" value="{{ old('postal_code') }}" required placeholder="10220"
                                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-sm font-semibold text-neutral-700">Alamat Lengkap</label>
                            <textarea name="full_address" id="field-full_address" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, patokan..."
                                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">{{ old('full_address') }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-sm font-semibold text-neutral-700">Catatan (opsional)</label>
                            <textarea name="note" rows="2" placeholder="Catatan untuk kurir atau penjual"
                                class="mt-1.5 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:bg-white">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                    <h2 class="flex items-center gap-2 text-lg font-extrabold text-neutral-900">
                        <x-icon name="truck" class="h-5 w-5 text-brand-500" /> Jasa Pengiriman
                    </h2>
                    <div class="mt-5 space-y-3" id="shipping-options">
                        <p class="rounded-xl bg-neutral-50 p-4 text-sm text-neutral-500">Pilih provinsi untuk melihat ongkir.</p>
                    </div>
                    <input type="hidden" name="courier" id="input-courier">
                    <input type="hidden" name="service" id="input-service">
                </div>

                <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                    <h2 class="flex items-center gap-2 text-lg font-extrabold text-neutral-900">
                        <x-icon name="credit-card" class="h-5 w-5 text-brand-500" /> Metode Pembayaran
                    </h2>

                    <div class="mt-5 space-y-3">
                        @foreach ($paymentMethods as $value => $label)
                            <label class="block cursor-pointer rounded-2xl border border-neutral-200 p-4 transition hover:border-brand-400 has-checked:border-brand-500 has-checked:bg-brand-50/50">
                                <span class="flex items-center gap-4">
                                    <input type="radio" name="payment_method" value="{{ $value }}" @checked(old('payment_method', $value === 'midtrans') === $value)
                                        class="h-4 w-4 border-neutral-300 text-brand-500 focus:ring-brand-500" required>
                                    <span class="text-sm font-semibold text-neutral-800">{{ $label }}</span>
                                </span>

                                @if ($value === 'midtrans')
                                    <span class="mt-3 block border-t border-neutral-200 pt-3 text-xs text-neutral-500">
                                        Anda akan diarahkan ke halaman pembayaran Midtrans untuk memilih sendiri
                                        metode pembayaran: transfer bank, virtual account, QRIS, GoPay, atau e-wallet
                                        lain yang tersedia.
                                    </span>
                                @else
                                    <span class="mt-3 block border-t border-neutral-200 pt-3 text-xs text-neutral-500">
                                        Bayar tunai kepada kurir saat pesanan diterima. Pesanan diproses setelah
                                        pembayaran diterima.
                                    </span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <aside class="space-y-5 lg:sticky lg:top-32 lg:self-start">
                <div class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-extrabold text-neutral-900">Ringkasan Pesanan</h2>

                    <div class="mt-4 max-h-72 space-y-4 overflow-y-auto pr-1">
                        @foreach ($items as $item)
                            <div class="flex gap-3">
                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="h-16 w-16 shrink-0 rounded-xl object-cover">
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-semibold text-neutral-900">{{ $item->product->name }}</p>
                                    <p class="mt-0.5 text-xs text-neutral-500">
                                        {{ $item->quantity }}x
                                        @if ($item->size) &middot; {{ $item->size }} @endif
                                        @if ($item->color) &middot; {{ $item->color }} @endif
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-neutral-900">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <dl class="mt-5 space-y-3 border-t border-neutral-100 pt-5 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Subtotal</dt>
                            <dd class="font-semibold text-neutral-900">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</dd>
                        </div>
                        @if ($discount > 0)
                            <div class="flex justify-between text-green-600">
                                <dt>Diskon Voucher</dt>
                                <dd class="font-semibold">- {{ 'Rp ' . number_format($discount, 0, ',', '.') }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Biaya Pengiriman</dt>
                            <dd class="font-semibold text-neutral-900" id="summary-shipping">-</dd>
                        </div>
                        <div class="flex justify-between border-t border-neutral-100 pt-3 text-base">
                            <dt class="font-bold text-neutral-900">Total Pembayaran</dt>
                            <dd class="font-black text-brand-600" id="summary-total">{{ 'Rp ' . number_format(max($subtotal - $discount, 0), 0, ',', '.') }}</dd>
                        </div>
                    </dl>

                    <button type="submit"
                        class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600">
                        Buat Pesanan <x-icon name="arrow-right" class="h-5 w-5" />
                    </button>
                    <p class="mt-3 text-center text-xs text-neutral-400">Dengan membuat pesanan, kamu menyetujui syarat & ketentuan GoSport.</p>
                </div>
            </aside>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const baseTotal = {{ max($subtotal - $discount, 0) }};
        const shippingLabel = document.getElementById('summary-shipping');
        const totalLabel = document.getElementById('summary-total');
        const courierInput = document.getElementById('input-courier');
        const serviceInput = document.getElementById('input-service');
        const provinceInput = document.getElementById('field-province');
        const shippingOptionsContainer = document.getElementById('shipping-options');
        const shippingOptions = @json($shippingOptions);
        const shippingRegions = @json($shippingRegions);
        const oldCourier = @json(old('courier'));
        const oldService = @json(old('service'));

        const rupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

        const selectShipping = (radio) => {
            const fee = parseInt(radio.dataset.fee, 10) || 0;
            const [courier, service] = radio.value.split('|');
            courierInput.value = courier;
            serviceInput.value = service;
            shippingLabel.textContent = rupiah(fee);
            totalLabel.textContent = rupiah(baseTotal + fee);
        };

        const renderShippingOptions = () => {
            const region = shippingRegions.find((candidate) => candidate.provinces.includes(provinceInput.value));
            shippingOptionsContainer.innerHTML = '';
            courierInput.value = '';
            serviceInput.value = '';
            shippingLabel.textContent = '-';
            totalLabel.textContent = rupiah(baseTotal);

            if (! region) {
                shippingOptionsContainer.innerHTML = '<p class="rounded-xl bg-neutral-50 p-4 text-sm text-neutral-500">Pilih provinsi untuk melihat ongkir.</p>';

                return;
            }

            shippingOptions.forEach((courier) => {
                Object.entries(courier.services).forEach(([service, fee]) => {
                    const shippingFee = fee + region.surcharge;
                    const option = document.createElement('label');
                    option.className = 'flex cursor-pointer items-center gap-4 rounded-2xl border border-neutral-200 p-4 transition hover:border-brand-400 has-checked:border-brand-500 has-checked:bg-brand-50/50';
                    option.innerHTML = `
                        <input type="radio" name="courier_service" value="${courier.code}|${service}" data-fee="${shippingFee}" class="h-4 w-4 border-neutral-300 text-brand-500 focus:ring-brand-500" required>
                        <span class="flex-1">
                            <span class="block text-sm font-bold text-neutral-900">${courier.name} - ${service}</span>
                            <span class="block text-xs text-neutral-500">${region.name} · Estimasi 1-4 hari kerja</span>
                        </span>
                        <span class="text-sm font-extrabold text-neutral-900">${rupiah(shippingFee)}</span>`;
                    const radio = option.querySelector('input');
                    radio.checked = courier.code === oldCourier && service === oldService;
                    shippingOptionsContainer.append(option);
                });
            });

            const selected = shippingOptionsContainer.querySelector('input:checked');
            if (selected) {
                selectShipping(selected);
            }
        };

        shippingOptionsContainer.addEventListener('change', (event) => {
            if (event.target.name === 'courier_service') {
                selectShipping(event.target);
            }
        });

        provinceInput.addEventListener('change', renderShippingOptions);
        renderShippingOptions();

        document.querySelectorAll('[data-address]').forEach((button) => {
            button.addEventListener('click', () => {
                const data = JSON.parse(button.dataset.address);
                Object.entries(data).forEach(([key, value]) => {
                    const field = document.getElementById('field-' + key);
                    if (field) field.value = value;
                });
                provinceInput.dispatchEvent(new Event('change'));
                document.querySelectorAll('[data-address]').forEach((b) => b.classList.remove('border-brand-500', 'bg-brand-50/60'));
                button.classList.add('border-brand-500', 'bg-brand-50/60');
            });
        });
    </script>
@endpush
