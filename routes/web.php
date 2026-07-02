<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Booking\AdminRentalController;
use App\Http\Controllers\Booking\RentalController as FrontendRentalController;
use App\Http\Controllers\Catalog\AdminCarController;
use App\Http\Controllers\Catalog\CarController as FrontendCarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\CustomerDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Payment\AdminPaymentController;
use App\Http\Controllers\Payment\PaymentProofController as FrontendPaymentProofController;
use App\Http\Controllers\Payment\PaymentProofDocumentController;
use App\Http\Controllers\Verification\AdminVerificationController;
use App\Http\Controllers\Verification\VerificationController as FrontendVerificationController;
use App\Http\Controllers\Verification\VerificationDocumentController;
use App\Models\Car;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', function () {
    return Inertia::render('Frontend/AboutPage');
})->name('about');
Route::get('/contact', function () {
    return Inertia::render('Frontend/ContactPage');
})->name('contact');
Route::get('/cars', [FrontendCarController::class, 'index'])->name('cars');
Route::get('/cars/{car}/availability', [FrontendCarController::class, 'availability'])->name('cars.availability');
Route::get('/cars/{id}', [FrontendCarController::class, 'show'])->name('cars.show');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/cars', [AdminCarController::class, 'index'])->name('cars.index');
        Route::get('/cars/create', [AdminCarController::class, 'create'])->name('cars.create');
        Route::post('/cars', [AdminCarController::class, 'store'])->name('cars.store');
        // Route::get('/cars/{id}', [AdminCarController::class, 'show'])->name('cars.show');
        Route::get('/cars/edit/{id}', [AdminCarController::class, 'edit'])->name('cars.edit');
        Route::post('/cars/{id}', [AdminCarController::class, 'update'])->name('cars.update');
        Route::delete('/cars/{id}', [AdminCarController::class, 'destroy'])->name('cars.destroy');


        // rental
        Route::get('/rentals', [AdminRentalController::class, 'index'])->name('rentals.index');
        Route::get('/rentals/edit/{rental}', [AdminRentalController::class, 'edit'])->name('rentals.edit');
        Route::post('/rentals/{rental}', [AdminRentalController::class, 'update'])->name('rentals.update');
        Route::delete('/rentals/delete/{rental}', [AdminRentalController::class, 'destroy'])->name('rentals.destroy');
        // Route::get('/rentals/{id}', [AdminRentalController::class, 'show'])->name('rentals.show');

        // customers
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
        Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        // customer verification
        Route::get('/verifications', [AdminVerificationController::class, 'index'])->name('verifications.index');
        Route::post('/verifications/{customer}/approve', [AdminVerificationController::class, 'approve'])->name('verifications.approve');
        Route::post('/verifications/{customer}/status', [AdminVerificationController::class, 'updateStatus'])->name('verifications.status');
    }
);

Route::middleware(['auth', 'payment.reviewer'])->prefix('admin')->group(
    function () {
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/edit/{payment}', [AdminPaymentController::class, 'edit'])->name('payments.edit');
        Route::post('/payments/{payment}', [AdminPaymentController::class, 'update'])->name('payments.update');
        Route::get('/payment-customers/{id}', [CustomerController::class, 'show'])->name('staff.customers.show');
    }
);

Route::middleware('auth')->group(function () {
    Route::get('/my-dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/fleet/availability', [CustomerDashboardController::class, 'availability'])->name('fleet.availability');
    Route::get('/verification', [FrontendVerificationController::class, 'index'])->name('customer.verification.index');
    Route::post('/verification', [FrontendVerificationController::class, 'store'])->name('customer.verification.store');
    Route::get('/verification/documents/{verification}/{document}', VerificationDocumentController::class)->name('customer.verification.document');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/bookings/confirm', [FrontendRentalController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/rentals', [FrontendRentalController::class, 'store'])->middleware(['customer.email.verified', 'customer.verified'])->name('rentals.store');
    Route::get('/my-bookings', [FrontendRentalController::class, 'index'])->name('bookings.index');
    Route::get('/my-bookings/{id}', [FrontendRentalController::class, 'show'])->name('bookings.show');
    Route::get('/my-bookings/{rental}/payment', [FrontendRentalController::class, 'payment'])->name('bookings.payment');
    Route::get('/cars/{id}/book', [FrontendCarController::class, 'book'])->name('cars.book');
    Route::post('/rentals/{rental}/cancel', [FrontendRentalController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/rentals/{rental}/payment-proof', [FrontendPaymentProofController::class, 'store'])->name('bookings.payment-proof.store');
    Route::get('/payments/{payment}/proof', PaymentProofDocumentController::class)->name('payments.proof');
});

require __DIR__.'/auth.php';
