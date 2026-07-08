<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// Public Routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/car/{id}', [PublicController::class, 'show'])->name('car.detail');
Route::post('/booking', [PublicController::class, 'storeBooking'])->name('booking.store');
Route::get('/track', [PublicController::class, 'track'])->name('booking.track');
Route::post('/track/otp', [PublicController::class, 'requestOtp'])->name('booking.track.otp');
Route::post('/track/verify', [PublicController::class, 'verifyOtp'])->name('booking.track.verify');
Route::post('/track/reset', [PublicController::class, 'resetTrackSession'])->name('booking.track.reset');
Route::post('/booking/{id}/upload-proof', [PublicController::class, 'uploadProof'])->name('booking.upload_proof');
Route::get('/invoice/{id}', [PublicController::class, 'invoice'])->name('booking.invoice');

// Admin Auth Routes (Security by Obscurity)
Route::redirect('/login', '/'); // Blokir & redirect /login biasa
Route::get('/pintu-akses-masuk-showdrive', [AuthController::class, 'showLogin'])->name('login');
Route::post('/pintu-akses-masuk-showdrive', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/items', [AdminController::class, 'manageItems'])->name('items');
    Route::get('/invoices', [AdminController::class, 'manageInvoices'])->name('invoices');
    Route::get('/warehouses', [AdminController::class, 'manageWarehouses'])->name('warehouses');
    Route::get('/cashiers', [AdminController::class, 'manageCashiers'])->name('cashiers');
    Route::get('/profile', [AdminController::class, 'manageProfile'])->name('profile');
    // CRUD Cars (Items)
    Route::post('/cars', [AdminController::class, 'storeCar'])->name('cars.store');
    Route::put('/cars/{id}', [AdminController::class, 'updateCar'])->name('cars.update');
    Route::delete('/cars/{id}', [AdminController::class, 'deleteCar'])->name('cars.destroy');

    // Admin Actions (Full-page redirect — legacy)
    Route::post('/booking/{id}/verify', [AdminController::class, 'verifyPayment'])->name('booking.verify');
    Route::post('/booking/{id}/status', [AdminController::class, 'processInspection'])->name('booking.status');

    // Admin AJAX Quick Actions (JSON response — for real-time UI updates)
    Route::post('/booking/{id}/verify-ajax', [AdminController::class, 'verifyPaymentAjax'])->name('booking.verify.ajax');
    Route::post('/booking/{id}/status-ajax', [AdminController::class, 'processInspectionAjax'])->name('booking.status.ajax');

    // CRUD Warehouses (Gudang)
    Route::post('/warehouses', [AdminController::class, 'storeWarehouse'])->name('warehouses.store');
    Route::put('/warehouses/{id}', [AdminController::class, 'updateWarehouse'])->name('warehouses.update');
    Route::delete('/warehouses/{id}', [AdminController::class, 'deleteWarehouse'])->name('warehouses.destroy');

    // CRUD Cashiers (Kasir/Admin)
    Route::post('/cashiers', [AdminController::class, 'storeCashier'])->name('cashiers.store');
    Route::put('/cashiers/{id}', [AdminController::class, 'updateCashier'])->name('cashiers.update');
    Route::delete('/cashiers/{id}', [AdminController::class, 'deleteCashier'])->name('cashiers.destroy');

    // Company Profile
    Route::put('/company', [AdminController::class, 'updateCompany'])->name('company.update');

    // Laporan Keuangan
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
});
