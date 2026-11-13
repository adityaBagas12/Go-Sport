<?php

return [
    'shipping' => [
        ['code' => 'jne', 'name' => 'JNE', 'services' => ['REG' => 15000, 'YES' => 30000]],
        ['code' => 'jnt', 'name' => 'J&T Express', 'services' => ['EZ' => 12000, 'Turbo' => 25000]],
        ['code' => 'sicepat', 'name' => 'SiCepat', 'services' => ['REG' => 14000, 'BEST' => 28000]],
        ['code' => 'pos', 'name' => 'POS Indonesia', 'services' => ['Kilat' => 18000, 'Reguler' => 11000]],
    ],

    // shortcut: fixed zone surcharges, replace with courier rate API when live quotes are required.
    'shipping_regions' => [
        ['name' => 'Jawa', 'surcharge' => 0, 'provinces' => ['Banten', 'DKI Jakarta', 'DI Yogyakarta', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur']],
        ['name' => 'Sumatera', 'surcharge' => 3500, 'provinces' => ['Aceh', 'Bengkulu', 'Jambi', 'Kepulauan Bangka Belitung', 'Kepulauan Riau', 'Lampung', 'Riau', 'Sumatera Barat', 'Sumatera Selatan', 'Sumatera Utara']],
        ['name' => 'Bali & Nusa Tenggara', 'surcharge' => 5000, 'provinces' => ['Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur']],
        ['name' => 'Kalimantan', 'surcharge' => 8000, 'provinces' => ['Kalimantan Barat', 'Kalimantan Selatan', 'Kalimantan Tengah', 'Kalimantan Timur', 'Kalimantan Utara']],
        ['name' => 'Sulawesi', 'surcharge' => 9000, 'provinces' => ['Gorontalo', 'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tengah', 'Sulawesi Tenggara', 'Sulawesi Utara']],
        ['name' => 'Maluku & Papua', 'surcharge' => 15000, 'provinces' => ['Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Barat Daya', 'Papua Pegunungan', 'Papua Selatan', 'Papua Tengah']],
    ],

    /**
     * Metode pembayaran yang benar-benar didukung aplikasi.
     *
     * Kunci di sini adalah nilai yang disimpan pada kolom
     * `orders.payment_method` dan `payments.payment_method`, dan harus
     * memiliki entri di `PaymentGatewayRegistry::CHANNELS`.
     *
     * Seluruh metode pembayaran noninstan (rekening, virtual account, QRIS,
     * GoPay, e-wallet) ditangani Midtrans Snap: customer memilih metodenya
     * sendiri di halaman Midtrans, bukan di halaman checkout.
     */
    'payment_methods' => [
        'midtrans' => 'Pembayaran Online (Midtrans)',
        'cod' => 'Bayar di Tempat (COD)',
    ],

    'payment' => [
        'gateway' => 'midtrans',

        /**
         * Masa berlaku transaksi Snap sebelum Midtrans mengirim status
         * `expire`. Berlaku untuk metode yang butuh bayar di muka.
         */
        'expiry_hours' => (int) env('MIDTRANS_EXPIRY_HOURS', 24),

        /**
         * Host yang dipakai untuk membentuk URL callback Snap (finish,
         * unfinish, error). Midtrans harus bisa menjangkau host ini, jadi
         * saat pengembangan lokal gunakan URL publik/tunnel, bukan
         * 127.0.0.1. Kosongkan untuk memakai `config('app.url')`.
         */
        'public_url' => env('MIDTRANS_PUBLIC_URL'),
    ],
];
