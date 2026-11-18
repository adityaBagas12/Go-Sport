<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/kategori/{slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/promo', [PageController::class, 'promo'])->name('pages.promo');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('pages.about');
Route::get('/kontak', [PageController::class, 'contact'])->name('pages.contact');

/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/lupa-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Payment Gateway Callback
|--------------------------------------------------------------------------
| Dipanggil langsung oleh server Midtrans setelah customer membayar,
| sehingga berada di luar middleware `auth` dan tanpa proteksi CSRF.
|
| Isi request tidak dipercaya mentah-mentah: `Midtrans\Notification`
| melakukan request balik ke API Midtrans berdasarkan `transaction_id`, dan
| endpoint ini satu-satunya yang boleh mengubah status menjadi lunas.
|
| PENTING: Midtrans tidak dapat menjangkau http://127.0.0.1. Saat
| developing di localhost, arahkan Notification URL ke URL publik/tunnel.
*/
Route::post('/midtrans/notification', [MidtransController::class, 'notification'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->middleware('throttle:120,1')
    ->name('midtrans.notification');

/*
|--------------------------------------------------------------------------
| Authenticated Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/tambah', [CartController::class, 'add'])->name('cart.add');
    Route::put('/keranjang/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/hapus/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/keranjang/voucher', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::delete('/keranjang/voucher', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::prefix('akun')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/profil', [AccountController::class, 'profile'])->name('profile');
        Route::put('/profil', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');

        Route::get('/alamat', [AccountController::class, 'addresses'])->name('addresses');
        Route::post('/alamat', [AccountController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/alamat/{id}/utama', [AccountController::class, 'setDefaultAddress'])->name('addresses.default');
        Route::delete('/alamat/{id}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');

        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::get('/pesanan', [AccountController::class, 'orders'])->name('orders');
    });

    Route::get('/pesanan/{orderNumber}', [OrderController::class, 'show'])->name('order.show');
    Route::post('/pesanan/{orderNumber}/batal', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::post('/ulasan', [ReviewController::class, 'store'])->name('reviews.store');

    // Halaman invoice + tombol "Bayar Sekarang" yang mengarahkan customer
    // ke halaman pembayaran Midtrans.
    Route::get('/invoice/{invoiceNumber}', [PaymentController::class, 'show'])->name('payment.invoice');
    Route::post('/invoice/{invoiceNumber}/bayar', [PaymentController::class, 'pay'])->name('payment.pay');

    // Halaman tujuan setelah customer keluar dari halaman Midtrans. Halaman
    // ini hanya menampilkan informasi; status final tetap dari notification.
    Route::get('/midtrans/finish', [MidtransController::class, 'finish'])->name('midtrans.finish');
    Route::get('/midtrans/unfinish', [MidtransController::class, 'unfinish'])->name('midtrans.unfinish');
    Route::get('/midtrans/error', [MidtransController::class, 'error'])->name('midtrans.error');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::get('/grafik-penjualan', Admin\SalesChartController::class)->name('dashboard.sales-chart');

    Route::resource('products', Admin\ProductController::class)->except('show');
    Route::resource('categories', Admin\CategoryController::class)->except('show');

    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/payment/verify', [Admin\OrderController::class, 'verifyPayment'])->name('orders.payment.verify');
    Route::delete('/orders/{order}', [Admin\OrderController::class, 'destroy'])->name('orders.destroy');

    Route::resource('coupons', Admin\CouponController::class)->except('show');

    Route::get('/customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [Admin\CustomerController::class, 'show'])->name('customers.show');
});
