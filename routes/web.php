<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RentalController as AdminRentalController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IdentityVerificationController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\RentalController as StaffRentalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product:slug}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{product:slug}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{product:slug}', [CartController::class, 'remove'])->name('cart.remove');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CartController::class, 'storeCheckout'])->name('checkout.store');

    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [OrdersController::class, 'show'])->name('orders.show');
    Route::post('/orders/{orderNumber}/cancel', [OrdersController::class, 'destroy'])->name('orders.cancel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/verification', [IdentityVerificationController::class, 'index'])->name('verification.index');
    Route::post('/verification', [IdentityVerificationController::class, 'store'])->name('verification.store');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/admin/verifications', [IdentityVerificationController::class, 'adminIndex'])->name('admin.verifications');
    Route::get('/admin/verifications/{user}/{type}', [IdentityVerificationController::class, 'showFile'])->name('admin.verifications.file');
    Route::post('/admin/verifications/{user}/approve', [IdentityVerificationController::class, 'approve'])->name('admin.verifications.approve');
    Route::post('/admin/verifications/{user}/reject', [IdentityVerificationController::class, 'reject'])->name('admin.verifications.reject');

    Route::get('/rentals', [AdminRentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/{rental}', [AdminRentalController::class, 'show'])->name('rentals.show');
    Route::patch('/rentals/{rental}/status', [AdminRentalController::class, 'updateStatus'])->name('rentals.update-status');
    Route::post('/rentals/{rental}/return', [AdminRentalController::class, 'returnRental'])->name('rentals.return');
    Route::post('/rentals/{rental}/payment/confirm', [AdminRentalController::class, 'confirmPayment'])->name('rentals.payment.confirm');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::delete('/rentals/bulk', [AdminRentalController::class, 'bulkDestroy'])->name('rentals.bulk-destroy');
    Route::resource('/admin/products', AdminProductController::class)
        ->except(['show', 'destroy'])
        ->names('admin.products');
    Route::resource('/admin/categories', AdminCategoryController::class)
        ->except(['show'])
        ->names('admin.categories');
    Route::resource('/admin/customers', AdminCustomerController::class)
        ->names('admin.customers');
    Route::get('/admin/settings', [AdminSettingController::class, 'edit'])->name('admin.settings.edit');
    Route::put('/admin/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');
});

Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/rentals', [StaffRentalController::class, 'index'])->name('staff.rentals');
});

Route::get('/pages/terms', [PagesController::class, 'terms'])->name('pages.terms');
Route::get('/pages/privacy', [PagesController::class, 'privacy'])->name('pages.privacy');
Route::get('/pages/contact', [PagesController::class, 'contact'])->name('pages.contact');

require __DIR__.'/auth.php';
