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
use App\Http\Controllers\ShopServicesController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopSetupController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\HasShop;
use App\Http\Middleware\IsAdmin;


// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/petlandingpage', [ShopController::class, 'index'])->name('petlandingpage');
Route::get('/book/{shop}', [BookingController::class, 'show'])->name('booking.show');
Route::get('/grooming', function () {
    return view('groomVetLandingPage.groominglandingpage');
})->name('grooming');
Route::get('/grooming-shops', [ShopController::class, 'groomingShops'])->name('groomingShops');
Route::get('/terms', function () { return view('pages.terms'); })->name('terms');
Route::get('/privacy', function () { return view('pages.privacy'); })->name('privacy');
Route::get('/faqs', function () { return view('pages.faqs'); })->name('faqs');

// Authentication routes
Auth::routes();

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Shop registration and setup routes
    Route::prefix('shop')->name('shop.')->group(function () {
        // Registration routes
        Route::get('/pre-register', [ShopRegistrationController::class, 'showPreRegistration'])->name('pre.register');
        Route::get('/register', [ShopRegistrationController::class, 'showRegistrationForm'])->name('register.form');
        Route::post('/register', [ShopRegistrationController::class, 'register'])->name('register');
        Route::get('/registration-pending', [ShopRegistrationController::class, 'showPendingApproval'])->name('registration.pending');
        
        // Setup routes (requires shop and checks setup status)
        Route::middleware([HasShop::class])->group(function () {
            Route::get('/setup', [ShopSetupController::class, 'welcome'])->name('setup.welcome');
            Route::get('/setup/services', [ShopSetupController::class, 'services'])->name('setup.services');
            Route::post('/setup/services', [ShopSetupController::class, 'storeServices'])->name('setup.services.store');
            Route::get('/setup/hours', [ShopSetupController::class, 'hours'])->name('setup.hours');
            Route::post('/setup/hours', [ShopSetupController::class, 'storeHours'])->name('setup.hours.store');
        });

        // Shop management routes (requires shop)
        Route::middleware([HasShop::class])->group(function () {
            Route::get('/dashboard', [ShopDashboardController::class, 'index'])->name('dashboard');
            Route::get('/profile', [ShopProfileController::class, 'show'])->name('profile');
            Route::put('/profile', [ShopProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/image', [ShopProfileController::class, 'updateImage'])->name('profile.update-image');
            Route::get('/appointments', [ShopAppointmentController::class, 'index'])->name('appointments');
            Route::post('/mode/customer', [ShopDashboardController::class, 'switchToCustomerMode'])->name('mode.customer');
            
            // Services management routes
            Route::get('/services', [ShopServicesController::class, 'index'])->name('services');
            Route::get('/services/{service}', [ShopServicesController::class, 'show'])->name('services.show');
            Route::post('/services', [ShopServicesController::class, 'store'])->name('services.store');
            Route::put('/services/{service}', [ShopServicesController::class, 'update'])->name('services.update');
            Route::delete('/services/{service}', [ShopServicesController::class, 'destroy'])->name('services.destroy');
            Route::put('/services/{service}/status', [ShopServicesController::class, 'updateStatus'])->name('services.update-status');
            
            // Static routes
            Route::view('/employees', 'shop.employees.index')->name('employees');
            Route::view('/analytics', 'shop.analytics.index')->name('analytics');
            Route::view('/settings', 'shop.settings.index')->name('settings');
        });
    });

    // Customer profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        // Profile routes
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update-info', [ProfileController::class, 'updatePersonalInfo'])->name('update-info');
        Route::post('/update-photo', [ProfileController::class, 'updateProfilePhoto'])->name('update-photo');
        Route::post('/update-location', [ProfileController::class, 'updateLocation'])->name('update-location');
        
        // Pet routes
        Route::prefix('pets')->name('pets.')->group(function () {
            // Pet CRUD operations
            Route::post('/', [ProfileController::class, 'storePet'])->name('store');
            Route::put('/{pet}', [ProfileController::class, 'updatePet'])->name('update');
            Route::delete('/{pet}', [ProfileController::class, 'deletePet'])->name('delete');
            Route::post('/{pet}/update-photo', [ProfileController::class, 'updatePetPhoto'])->name('update-photo');
            
            // Pet details and health records
            Route::get('/{pet}/details', [ProfileController::class, 'showPetDetails'])->name('details');
            Route::get('/{pet}/health-record', [ProfileController::class, 'showHealthRecord'])->name('health-record');
            Route::get('/{pet}/add-health-record', [ProfileController::class, 'showAddHealthRecord'])->name('add-health-record');
        });
    });

    // Booking routes
    Route::middleware(['auth', 'web'])->group(function () {
        // Time slots route
        Route::get('/time-slots/shop/{shop}', [BookingController::class, 'getTimeSlots'])
            ->name('time-slots.get');

        Route::prefix('book/{shop}')->name('booking.')->group(function () {
            Route::get('/process', [BookingController::class, 'process'])->name('process');
            Route::post('/select-service', [BookingController::class, 'selectService'])->name('select-service');
            Route::post('/select-datetime', [BookingController::class, 'selectDateTime'])->name('select-datetime');
            Route::get('/confirm', [BookingController::class, 'showConfirm'])->name('confirm.show');
            Route::post('/confirm', [BookingController::class, 'confirm'])->name('confirm');
            Route::post('/store', [BookingController::class, 'store'])->name('store');
            Route::get('/thank-you', [BookingController::class, 'thankYou'])->name('thank-you');
            Route::get('/receipt', [ReceiptController::class, 'download'])->name('receipt.download');
        });
    });

    // Appointment routes
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
        Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
        Route::get('/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('reschedule');
        Route::put('/{appointment}/reschedule', [AppointmentController::class, 'updateSchedule'])->name('update-schedule');
        Route::post('/{appointment}/mark-as-paid', [AppointmentController::class, 'markAsPaid'])->name('mark-as-paid');
        Route::post('/{appointment}/shop-cancel', [AppointmentController::class, 'shopCancel'])->name('shop-cancel');
        Route::post('/{appointment}/accept', [AppointmentController::class, 'accept'])->name('accept');
    });
    Route::resource('appointments', AppointmentController::class)->except(['show']);

    // Other customer routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{shop}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/shops/{shop}/review', [ShopController::class, 'submitReview'])->name('shops.review')->middleware('auth');
});

// Admin routes
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/shops', [AdminController::class, 'shops'])->name('shops');
    Route::post('/shops/{shop}/approve', [AdminController::class, 'approveShop'])->name('shops.approve');
    Route::post('/shops/{shop}/reject', [AdminController::class, 'rejectShop'])->name('shops.reject');
    Route::post('/shops/{shop}/toggle-status', [AdminController::class, 'toggleShopStatus'])->name('shops.toggle-status');
    Route::get('/shops/{shop}/analytics', [AdminController::class, 'getShopAnalytics'])->name('shops.analytics');
    
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/services', [AdminController::class, 'services'])->name('services');
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::get('/support', [AdminController::class, 'support'])->name('support');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
});

Route::middleware(['auth', 'has.shop'])->group(function () {
    Route::post('/shop/services', [ShopServicesController::class, 'store']);
    Route::put('/shop/services/{service}', [ShopServicesController::class, 'update']);
    Route::get('/shop/services/{service}', [ShopServicesController::class, 'show']);
    Route::delete('/shop/services/{service}', [ShopServicesController::class, 'destroy']);
    Route::put('/shop/services/{service}/status', [ShopServicesController::class, 'updateStatus']);
});
