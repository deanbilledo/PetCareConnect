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
use App\Http\Controllers\ShopProfileController;
use App\Http\Controllers\ShopAppointmentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/petlandingpage', [ShopController::class, 'index'])->name('petlandingpage');
Route::get('/book/{shop}', [BookingController::class, 'show'])->name('booking.show');

// Authentication routes
Auth::routes();

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Move this route before the resource route to prevent conflicts
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
        ->name('appointments.cancel');
    
    Route::resource('appointments', AppointmentController::class);
    
    // Booking process routes (protected)
    Route::get('/book/{shop}/process', [BookingController::class, 'process'])->name('booking.process');
    Route::post('/book/{shop}/select-service', [BookingController::class, 'selectService'])->name('booking.select-service');
    Route::post('/book/{shop}/select-datetime', [BookingController::class, 'selectDateTime'])->name('booking.select-datetime');
    Route::post('/book/{shop}/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::post('/book/{shop}/store', [BookingController::class, 'store'])->name('booking.store');
    
    // Other protected routes...
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-info', [ProfileController::class, 'updatePersonalInfo'])->name('profile.update-info');
    Route::post('/profile/update-photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.update-photo');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/profile/update-location', [ProfileController::class, 'updateLocation'])->name('profile.update-location');
    Route::post('/profile/pets', [ProfileController::class, 'storePet'])->name('profile.pets.store');
    Route::put('/profile/pets/{pet}', [ProfileController::class, 'updatePet'])->name('profile.pets.update');
    Route::delete('/profile/pets/{pet}', [ProfileController::class, 'deletePet'])->name('profile.pets.delete');
    
    // Add this new route
    Route::get('/booking/thank-you', function () {
        if (!session()->has('booking_details')) {
            return redirect()->route('home');
        }
        return view('booking.thank-you');
    })->name('booking.thank-you');

    // Add this inside your auth middleware group
    Route::post('/shop/{shop}/review', [ShopController::class, 'submitReview'])->name('shop.review');

    Route::get('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])
        ->name('appointments.reschedule');
    Route::put('/appointments/{appointment}/reschedule', [AppointmentController::class, 'updateSchedule'])
        ->name('appointments.update-schedule');

    // Shop Dashboard Routes
    Route::get('/shop/dashboard', [ShopDashboardController::class, 'index'])->name('shop.dashboard');
    Route::get('/switch-to-customer', [ShopDashboardController::class, 'switchToCustomerMode'])->name('switch.to.customer');

    // Shop Profile Routes
    Route::middleware(['auth', \App\Http\Middleware\HasShop::class])->group(function () {
        Route::get('/shop/profile', [ShopProfileController::class, 'show'])->name('shop.profile');
        Route::put('/shop/profile', [ShopProfileController::class, 'update'])->name('shop.profile.update');
        Route::post('/shop/profile/image', [ShopProfileController::class, 'updateImage'])->name('shop.profile.update-image');
        Route::get('/shop/appointments', [ShopAppointmentController::class, 'index'])->name('shop.appointments');

        // Static routes using closures (no controllers yet)
        Route::get('/shop/services', function() {
            return view('shop.services.index');
        })->name('shop.services');

        Route::get('/shop/employees', function() {
            return view('shop.employees.index');
        })->name('shop.employees');
        
        Route::get('/shop/analytics', function() {
            return view('shop.analytics.index');
        })->name('shop.analytics');

        // Add settings route with closure
        Route::get('/shop/settings', function() {
            return view('shop.settings.index');
        })->name('shop.settings');
    });

    Route::post('/appointments/{appointment}/mark-as-paid', [AppointmentController::class, 'markAsPaid'])
        ->name('appointments.mark-as-paid');
    Route::post('/appointments/{appointment}/shop-cancel', [AppointmentController::class, 'shopCancel'])
        ->name('appointments.shop-cancel');

    // Inside the auth middleware group, add these routes
    Route::get('/profile/pets/{pet}/details', [ProfileController::class, 'showPetDetails'])
        ->name('profile.pets.details');

    Route::get('/profile/pets/{pet}/add-health-record', function(Pet $pet) {
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }
        return view('profile.pets.add-health-record', compact('pet'));
    })->name('profile.pets.add-health-record');

    // Add this inside your auth middleware group
    Route::post('/profile/pets/{pet}/update-photo', [ProfileController::class, 'updatePetPhoto'])
        ->name('profile.pets.update-photo');

    // Add these routes inside your auth middleware group
    Route::post('/profile/pets/{pet}/store-vaccination', [ProfileController::class, 'storeVaccination'])
        ->name('profile.pets.store-vaccination');

    // Add this inside your auth middleware group
    Route::get('/pets/{pet}/health-record', function(App\Models\Pet $pet) {
        if ($pet->user_id !== auth()->id() && !auth()->user()->shop) {
            abort(403);
        }
        return view('pets.health-record', compact('pet'));
    })->name('pets.health-record');
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
    Route::post('/appointments/{appointment}/accept', [AppointmentController::class, 'accept'])
        ->name('appointments.accept');
});

// Add this with your existing routes
Route::get('/grooming-shops', function() {
    return view('groomVetLandingPage.groominglandingpage');
})->name('groomingShops');
