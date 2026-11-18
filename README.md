# GoSport — Sistem E-Commerce Perlengkapan & Pakaian Olahraga

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Midtrans](https://img.shields.io/badge/Payment-Midtrans_Sandbox-002B49?style=for-the-badge)](https://midtrans.com)
[![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)

Proyek Akhir / Tugas Besar Praktikum Pemrograman E-Commerce berbasis framework **Laravel 11**, database relasional **MySQL**, dan integrasi payment gateway **Midtrans Snap Sandbox**.

---

## 📋 Daftar Isi
1. [Deskripsi Singkat](#-deskripsi-singkat)
2. [Hak Akses & Fitur Sistem](#-hak-akses--fitur-sistem)
3. [Struktur Database & Relasi](#-struktur-database--relasi)
4. [Persyaratan Sistem](#-persyaratan-sistem)
5. [Panduan Instalasi & Menjalankan](#-panduan-instalasi--menjalankan)
6. [Akun Pengujian (Seeder)](#-akun-pengujian-seeder)
7. [Konfigurasi Midtrans Sandbox](#-konfigurasi-midtrans-sandbox)
8. [Pengujian Otomatis (Testing)](#-pengujian-otomatis-testing)

---

## 🎯 Deskripsi Singkat
**GoSport** adalah platform web e-commerce modern yang menjual berbagai macam perlengkapan dan pakaian olahraga (sepatu, jersey tim, perlengkapan gym, raket, aksesoris olahraga). Sistem ini menerapkan arsitektur **Model-View-Controller (MVC)**, database relasional dengan **Eloquent ORM**, autentikasi berbasis role (Admin & Pelanggan), keranjang belanja dinamis, checkout multi-kurir, pemotongan stok otomatis berdasarkan varian (ukuran & warna), serta pembayaran otomatis menggunakan **Midtrans Sandbox**.

---

## 👥 Hak Akses & Fitur Sistem

### 1. Role Admin
Admin bertanggung jawab atas manajemen operasional toko daring:
- **Autentikasi & Proteksi Akses**: Login khusus dengan middleware `admin` (`abort(403)` jika bukan admin).
- **Dashboard Analitik**:
  - Total Produk, Total Kategori, Total Pelanggan, Total Pesanan.
  - Jumlah pesanan menunggu pembayaran & jumlah pesanan lunas.
  - Total omzet pendapatan toko.
  - Grafik tren penjualan bulanan dinamis.
  - Daftar produk terlaris & pesanan terbaru.
- **Manajemen Kategori**: CRUD kategori produk dengan proteksi integritas relasi (kategori yang masih memiliki produk tidak dapat dihapus sembarangan).
- **Manajemen Produk & Varian**:
  - CRUD produk (nama, slug otomatis, kategori, harga, harga coret/diskon, badge, stok, gambar, deskripsi).
  - Manajemen stok varian (ukuran dan warna produk) terintegrasi.
- **Manajemen Pesanan**:
  - Melihat seluruh pesanan masuk beserta rincian item, data pelanggan, alamat, dan total biaya.
  - Update status pesanan (`menunggu_pembayaran`, `diproses`, `dikirim`, `selesai`, `dibatalkan`).
  - Sinkronisasi status pembayaran langsung ke server API Midtrans (`verifyPayment` / `syncWithMidtrans`).
  - Proteksi: Pesanan metode Midtrans tidak bisa diubah lunas sembarangan tanpa verifikasi gateway.
- **Manajemen Voucher / Kupon Promo**: CRUD kupon diskon (persentase atau nominal rupiah) dengan kuota dan masa berlaku.
- **Data Pelanggan**: Melihat daftar pelanggan terdaftar beserta riwayat transaksinya.

### 2. Role Pelanggan (Customer)
Pelanggan dapat menjelajahi produk dan melakukan transaksi belanja:
- **Autentikasi & Akun**:
  - Registrasi akun, Login, Logout, Lupa & Reset Password.
  - Dashboard akun pelanggan, edit profil, ubah kata sandi.
  - Buku alamat pengiriman (bisa menambah banyak alamat & memilih alamat utama).
  - Daftar wishlist (produk favorit).
- **Katalog & Detail Produk**:
  - Menampilkan daftar produk dengan pagination dan badge (Promo, Baru, Terlaris).
  - Pencarian produk (*live search*) & filter berdasarkan kategori.
  - Filter rentang harga (min/max price) dan sorting (harga terendah, tertinggi, terbaru, terlaris).
  - Halaman detail produk lengkap dengan galeri foto, pilihan varian (ukuran/warna), stok real-time, rating bintang, ulasan pembeli, dan rekomendasi produk terkait.
- **Keranjang Belanja (Shopping Cart)**:
  - Popup pemilihan varian cepat saat klik "Keranjang" di halaman katalog.
  - Tambah, ubah jumlah (stepper +/-), dan hapus item dari keranjang.
  - Validasi pencegahan kuantitas melebihi stok yang tersedia.
  - Pemasangan kode kupon voucher dengan kalkulasi diskon otomatis.
  - Kalkulasi subtotal dan estimasi total belanja.
- **Checkout & Pemesanan**:
  - Pemilihan produk yang ingin di-checkout (mendukung partial checkout).
  - Pengisian nama penerima, nomor HP, dan alamat pengiriman (atau memilih dari buku alamat).
  - Pemilihan kurir pengiriman (JNE, POS Indonesia, SiCepat) dengan kalkulasi ongkos kirim server-side.
  - Validasi ketat stok sebelum pembuatan pesanan (`atomic transaction`).
  - Pemotongan otomatis stok produk & varian saat pesanan terbentuk.
- **Pembayaran Midtrans**:
  - Halaman invoice pesanan resmi dengan nomor invoice unik (`GS-INV-YYYYMMDD-XXXX`).
  - Pembayaran langsung via **Midtrans Snap Sandbox** (Virtual Account BCA/BNI/BRI/Mandiri, Gopay, QRIS, Kartu Kredit).
  - Webhook callback handler (`/midtrans/notification`) untuk sinkronisasi otomatis status transaksi secara *real-time*.
- **Riwayat Pesanan & Ulasan**:
  - Daftar riwayat transaksi pesanan beserta status pembayaran dan pengiriman.
  - Halaman detail pesanan & invoice.
  - Pembatalan pesanan mandiri selama pesanan belum diproses/dibayar.
  - Pemberian rating bintang (1-5) dan ulasan teks setelah barang dibeli dan diterima.

---

## 🗄️ Struktur Database & Relasi

Sistem menggunakan 13 tabel migrasi relasional:
- `users`: Data pengguna beserta penanda peran (`role`: 'admin' / 'customer').
- `categories`: Kategori produk olahraga (`hasMany` products).
- `products`: Informasi dasar produk, harga, stok master, kategori (`belongsTo` category).
- `product_variants`: Varian ukuran, warna, dan stok per-kombinasi (`belongsTo` product).
- `product_images`: Galeri foto tambahan produk.
- `cart_items`: Keranjang belanja pengguna (`belongsTo` user & product).
- `wishlists`: Daftar produk yang disukai pengguna.
- `addresses`: Buku alamat pengiriman pengguna.
- `coupons`: Voucher diskon promo toko.
- `orders`: Data transaksi pesanan, status, informasi pengiriman, total tagihan.
- `order_items`: Rincian item produk di dalam setiap pesanan.
- `payments`: Data invoice, status verifikasi Midtrans, token Snap, dan waktu kedaluwarsa.
- `reviews`: Ulasan dan rating bintang produk dari pembeli terverifikasi.

---

## ⚙️ Persyaratan Sistem
- **PHP**: versi 8.2 atau 8.3 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `curl`, `gd`)
- **Composer**: versi 2.x
- **Node.js & NPM**: Node 18+ / 20+
- **Database**: MySQL 8.x atau MariaDB 10.4+ (misalnya melalui Laragon / XAMPP)

---

## 🚀 Panduan Instalasi & Menjalankan

1. **Clone Repository**:
   ```bash
   git clone https://github.com/<username>/<nama-repo>.git
   cd ecommerce1
   ```

2. **Install Dependensi PHP & JavaScript**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment (`.env`)**:
   Salin file konfigurasi contoh:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan sesuaikan pengaturan database MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ecommerce1
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Migrasi Database & Menjalankan Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Kompilasi Aset Frontend (Tailwind & Vite)**:
   ```bash
   npm run build
   ```

7. **Menjalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses di browser melalui: `http://localhost:8000` (atau `http://ecommerce1.test` jika menggunakan Laragon virtual host).

---

## 🔑 Akun Pengujian (Seeder)

Database seeder telah menyiapkan akun siap pakai:

| Peran (Role) | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@gosport.test` | `password` | Mengakses dashboard di `/admin` |
| **Pelanggan** | `customer@gosport.test` | `password` | Belanja, keranjang, checkout, riwayat pesanan |

---

## 💳 Konfigurasi Midtrans Sandbox

1. Buat akun di [Midtrans Sandbox](https://dashboard.sandbox.midtrans.com).
2. Ambil **Server Key** dan **Client Key** pada menu **Settings > Access Keys**.
3. Masukkan ke file `.env`:
   ```env
   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxxx
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxxx
   MIDTRANS_IS_PRODUCTION=false
   ```
4. **Pengujian Webhook Notification (Lokal)**:
   Karena Midtrans tidak dapat mengakses `127.0.0.1` secara langsung, gunakan tunnel seperti **Ngrok** atau **Cloudflared**:
   ```bash
   ngrok http 8000
   ```
   Lalu masukkan URL tunnel pada menu **Settings > Configuration > Payment Notification URL** di dashboard Midtrans:
   `https://<id-tunnel>.ngrok-free.app/midtrans/notification`

---

## 🧪 Pengujian Otomatis (Testing)

Proyek ini dilengkapi dengan unit dan feature tests komprehensif menggunakan **PHPUnit** untuk memvalidasi alur bisnis, proteksi hak akses, keranjang belanja, checkout, dan ulasan produk:

```bash
php artisan test --compact
```
*(Hasil: 74 tests passed, 283 assertions)*
