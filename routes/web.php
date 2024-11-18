<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ShopRegistrationController;
use App\Http\Controllers\ShopDashboardController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/petlandingpage', [ShopController::class, 'index'])->name('petlandingpage');

// Authentication routes
Auth::routes();

// Protected routes
Route::middleware(['auth'])->group(function () {
    
    Route::resource('appointments', AppointmentController::class);
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-info', [ProfileController::class, 'updatePersonalInfo'])->name('profile.update-info');
    Route::post('/profile/update-photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.update-photo');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/profile/update-location', [ProfileController::class, 'updateLocation'])->name('profile.update-location');
    Route::post('/profile/pets', [ProfileController::class, 'storePet'])->name('profile.pets.store');
    Route::put('/profile/pets/{pet}', [ProfileController::class, 'updatePet'])->name('profile.pets.update');
    Route::delete('/profile/pets/{pet}', [ProfileController::class, 'deletePet'])->name('profile.pets.delete');
    
    // Booking routes
    Route::get('/book/{shop}', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/book/{shop}/process', [BookingController::class, 'process'])->name('booking.process');
    Route::post('/book/{shop}/select-service', [BookingController::class, 'selectService'])->name('booking.select-service');
    Route::post('/book/{shop}/select-datetime', [BookingController::class, 'selectDateTime'])->name('booking.select-datetime');
    Route::post('/book/{shop}/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::post('/book/{shop}/store', [BookingController::class, 'store'])->name('booking.store');
    
    // Add this new route
    Route::get('/booking/thank-you', function () {
        if (!session()->has('booking_details')) {
            return redirect()->route('home');
        }
        return view('booking.thank-you');
    })->name('booking.thank-you');
});

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

// Shop Registration Routes
Route::prefix('shop')->name('shop.')->group(function () {
    // Pre-registration routes
    Route::get('/pre-register', [ShopRegistrationController::class, 'showPreRegistration'])->name('pre.register');
    Route::post('/pre-register', [ShopRegistrationController::class, 'handlePreRegistration'])->name('pre.register.submit');
    
    // Main registration routes - ensure these are protected by auth middleware
    Route::middleware(['auth'])->group(function () {
        Route::get('/register', [ShopRegistrationController::class, 'showRegistrationForm'])->name('register.form');
        Route::post('/register', [ShopRegistrationController::class, 'register'])->name('register');
    });
});

Route::middleware(['auth', \App\Http\Middleware\HasShop::class])->group(function () {
    Route::get('/shop/dashboard', [ShopDashboardController::class, 'index'])->name('shop.dashboard');
    Route::post('/shop/mode/customer', [ShopDashboardController::class, 'switchToCustomerMode'])->name('shop.mode.customer');
});

// Add this with your existing routes
Route::get('/grooming-shops', [ShopController::class, 'groomingShops'])->name('groomingShops');
