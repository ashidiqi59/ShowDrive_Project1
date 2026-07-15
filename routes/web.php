<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

// Public controllers
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;

// Admin controllers (namespace App\Http\Controllers\Admin)
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\PaymentSettingsController;
use App\Http\Controllers\Admin\ReportController;

// =====================================================================
// RATE LIMITERS
// =====================================================================

RateLimiter::for('otp-request', function (Request $request) {
    // Batas permintaan OTP baru: maks 5x per 10 menit per IP (ketat, untuk mencegah SMS/WA flood)
    return Limit::perMinutes(10, 5)->by($request->ip());
});

RateLimiter::for('otp-verify', function (Request $request) {
    // Batas percobaan verifikasi OTP: maks 20x per 10 menit per IP
    // Batasan sebenarnya ditangani di session (maks 3 kali gagal per sesi OTP via SEC-03)
    // Rate limiter ini hanya sebagai jaring pengaman terakhir dari serangan multi-sesi
    return Limit::perMinutes(10, 20)->by($request->ip());
});

RateLimiter::for('booking', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip());
});

RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

// =====================================================================
// PUBLIC ROUTES
// =====================================================================

Route::get('/', [PublicController::class, 'index'])->name('home');

Route::get('/car/{id}', [PublicController::class, 'show'])->name('car.detail')
    ->whereNumber('id');

Route::post('/booking', [PublicController::class, 'storeBooking'])->name('booking.store')
    ->middleware('throttle:booking');

Route::get('/track', [PublicController::class, 'track'])->name('booking.track');

Route::post('/track/otp', [PublicController::class, 'requestOtp'])->name('booking.track.otp')
    ->middleware('throttle:otp-request');

Route::post('/track/verify', [PublicController::class, 'verifyOtp'])->name('booking.track.verify')
    ->middleware('throttle:otp-verify');

Route::post('/track/reset', [PublicController::class, 'resetTrackSession'])->name('booking.track.reset');

Route::post('/booking/{id}/upload-proof', [PublicController::class, 'uploadProof'])->name('booking.upload_proof')
    ->whereNumber('id');

Route::post('/booking/{id}/cancel', [PublicController::class, 'cancelBooking'])->name('booking.cancel')
    ->whereNumber('id');

Route::get('/invoice/{id}', [PublicController::class, 'invoice'])->name('booking.invoice')
    ->whereNumber('id');

// =====================================================================
// ADMIN AUTH ROUTES
// =====================================================================

Route::redirect('/login', '/');

// Relay endpoint untuk shortcut keyboard kasir (Ctrl+Shift+A) dialihkan secara aman
Route::get('/pintu-masuk', [AuthController::class, 'shortcutRedirect'])->name('pintu.masuk');

Route::get('/pintu-akses-masuk-showdrive', [AuthController::class, 'showLogin'])->name('login')
    ->middleware('guest');

Route::post('/pintu-akses-masuk-showdrive', [AuthController::class, 'authenticate'])
    ->middleware(['guest', 'throttle:login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')
    ->middleware('auth');

// =====================================================================
// PROTECTED ADMIN ROUTES
// =====================================================================

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::redirect('/', '/admin/dashboard');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Items (Kendaraan)
    Route::get('/items',         [ItemController::class, 'index'])->name('items');
    Route::post('/cars',         [ItemController::class, 'store'])->name('cars.store');
    Route::put('/cars/{id}',     [ItemController::class, 'update'])->name('cars.update')->whereNumber('id');
    Route::delete('/cars/{id}',  [ItemController::class, 'destroy'])->name('cars.destroy')->whereNumber('id');

    // Invoices (Pembayaran & Inspeksi)
    Route::get('/invoices',                        [InvoiceController::class, 'index'])->name('invoices');
    Route::post('/booking/{id}/verify-ajax',       [InvoiceController::class, 'verifyAjax'])->name('booking.verify.ajax')->whereNumber('id');
    Route::post('/booking/{id}/status-ajax',       [InvoiceController::class, 'inspectionAjax'])->name('booking.status.ajax')->whereNumber('id');
    Route::post('/booking/{id}/cancel-ajax',         [InvoiceController::class, 'cancelAjax'])->name('booking.cancel.ajax')->whereNumber('id');
    Route::post('/booking/{id}/approve-ajax',        [InvoiceController::class, 'approveAjax'])->name('booking.approve.ajax')->whereNumber('id');
    Route::post('/booking/{id}/reject-ajax',         [InvoiceController::class, 'rejectAjax'])->name('booking.reject.ajax')->whereNumber('id');
    Route::post('/booking/{id}/update-customer-ajax',   [InvoiceController::class, 'updateCustomerAjax'])->name('booking.update.customer.ajax')->whereNumber('id');
    Route::post('/booking/{id}/amend-ajax',             [InvoiceController::class, 'amendAjax'])->name('booking.amend.ajax')->whereNumber('id');
    Route::post('/booking/{id}/handover-ajax',          [InvoiceController::class, 'confirmHandover'])->name('booking.handover.ajax')->whereNumber('id');

    // Warehouses (Gudang)
    Route::get('/warehouses',          [WarehouseController::class, 'index'])->name('warehouses');
    Route::post('/warehouses',         [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::put('/warehouses/{id}',     [WarehouseController::class, 'update'])->name('warehouses.update')->whereNumber('id');
    Route::delete('/warehouses/{id}',  [WarehouseController::class, 'destroy'])->name('warehouses.destroy')->whereNumber('id');

    // Cashiers (Kasir / Staf)
    Route::get('/cashiers',           [CashierController::class, 'index'])->name('cashiers');
    Route::post('/cashiers',          [CashierController::class, 'store'])->name('cashiers.store');
    Route::put('/cashiers/{id}',      [CashierController::class, 'update'])->name('cashiers.update')->whereNumber('id');
    Route::delete('/cashiers/{id}',   [CashierController::class, 'destroy'])->name('cashiers.destroy')->whereNumber('id');

    // Company Profile
    Route::get('/profile',   [CompanyController::class, 'edit'])->name('profile');
    Route::put('/company',   [CompanyController::class, 'update'])->name('company.update');

    // Payment Settings (Rekening & QRIS)
    Route::get('/payment-settings', [PaymentSettingsController::class, 'edit'])->name('payment_settings');
    Route::put('/payment-settings', [PaymentSettingsController::class, 'update'])->name('payment_settings.update');

    // Laporan Keuangan
    Route::get('/laporan',   [ReportController::class, 'index'])->name('laporan');
});
