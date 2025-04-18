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
use App\Http\Controllers\PetController;
use App\Http\Controllers\ShopEmployeeController;
use App\Http\Controllers\ShopEmployeeSetupController;
use App\Http\Controllers\ShopAnalyticsController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminServicesController;
use App\Http\Controllers\ShopSettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ShopReportController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\AppealController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\HasShop;
use App\Http\Middleware\IsAdmin;
use Illuminate\Http\Request;

// Include shop routes
require __DIR__.'/shop.php';

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/petlandingpage', [\App\Http\Controllers\VeterinaryController::class, 'index'])->name('petlandingpage');
Route::get('/book/{shop}', [BookingController::class, 'show'])->name('booking.show');
Route::get('/grooming', [\App\Http\Controllers\GroomingController::class, 'index'])->name('grooming');
Route::get('/grooming-shops', [ShopController::class, 'groomingShops'])->name('groomingShops');
Route::get('/shops/search-location', [ShopController::class, 'searchByLocation'])->name('shops.searchByLocation');
Route::get('/shops/all', [ShopController::class, 'getAllShops'])->name('shops.getAllShops');
Route::get('/terms', function () { return view('pages.terms'); })->name('terms');
Route::get('/privacy', function () { return view('pages.privacy'); })->name('privacy');
Route::get('/faqs', function () { return view('pages.faqs'); })->name('faqs');
Route::get('/pet-care-tips', function () {
    return view('pet-care-tips');
})->name('pet-care-tips');

// API routes for search
Route::get('/api/search/grooming-shops', [\App\Http\Controllers\GroomingController::class, 'searchShops']);
Route::get('/api/search/veterinary-shops', [\App\Http\Controllers\VeterinaryController::class, 'searchShops']);

// Authentication routes
Auth::routes();

// Social authentication routes
Route::get('login/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('login/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Shop registration and setup routes
    Route::prefix('shop')->name('shop.')->group(function () {
        // Registration routes
        Route::get('/pre-register', [ShopRegistrationController::class, 'showPreRegistration'])->name('pre.register');
        Route::get('/register', [ShopRegistrationController::class, 'showRegistrationForm'])->name('register.form');
        Route::post('/register', [ShopRegistrationController::class, 'register'])->name('register');
        Route::get('/registration-pending', [ShopRegistrationController::class, 'showPendingApproval'])->name('registration.pending');
        
        // Shop approval/rejection notification routes
        Route::post('/registration/{shop}/approve', [ShopRegistrationController::class, 'handleApproval'])
            ->name('registration.approve')
            ->middleware('auth', IsAdmin::class);
        Route::post('/registration/{shop}/reject', [ShopRegistrationController::class, 'handleRejection'])
            ->name('registration.reject')
            ->middleware('auth', IsAdmin::class);
        
        // Setup routes (requires shop and checks setup status)
        Route::middleware([\App\Http\Middleware\HasShop::class, \App\Http\Middleware\CheckShopSetup::class])->group(function () {
            // Setup Welcome
            Route::get('/setup/welcome', [ShopSetupController::class, 'welcome'])->name('setup.welcome');
            
            // Setup Details
            Route::get('/setup/details', [ShopSetupController::class, 'details'])->name('setup.details');
            Route::post('/setup/details', [ShopSetupController::class, 'storeDetails'])->name('setup.details.store');
            
            // Setup Services
            Route::get('/setup/services', [ShopSetupController::class, 'services'])->name('setup.services');
            Route::post('/setup/services', [ShopSetupController::class, 'storeServices'])->name('setup.services.store');
            
            // Setup Hours
            Route::get('/setup/hours', [ShopSetupController::class, 'hours'])->name('setup.hours');
            Route::post('/setup/hours', [ShopSetupController::class, 'storeHours'])->name('setup.hours.store');
            
            // Setup Employees
            Route::prefix('setup/employees')->name('setup.employees.')->group(function () {
                Route::get('/', [ShopEmployeeSetupController::class, 'index'])->name('index');
                Route::post('/', [ShopEmployeeSetupController::class, 'store'])->name('store');
                Route::get('/{employee}', [ShopEmployeeSetupController::class, 'show'])->name('show');
                Route::put('/{employee}', [ShopEmployeeSetupController::class, 'update'])->name('update');
                Route::delete('/{employee}', [ShopEmployeeSetupController::class, 'destroy'])->name('destroy');
            });

            // Subscriptions
            Route::get('/subscriptions', [App\Http\Controllers\Shop\SubscriptionController::class, 'index'])->name('subscriptions.index');
            Route::post('/subscriptions/verify', [App\Http\Controllers\Shop\SubscriptionController::class, 'verifyPayment'])->name('subscriptions.verify');
            Route::post('/subscriptions/cancel', [App\Http\Controllers\Shop\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        });

        // Shop management routes (requires shop)
        Route::middleware([\App\Http\Middleware\HasShop::class])->group(function () {
            Route::get('/dashboard', [ShopDashboardController::class, 'index'])->name('dashboard');
            Route::get('/profile', [ShopProfileController::class, 'show'])->name('profile');
            Route::put('/profile', [ShopProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/image', [ShopProfileController::class, 'updateImage'])->name('profile.update-image');
            Route::get('/appointments', [ShopAppointmentController::class, 'index'])->name('appointments');
            Route::get('/appointments/{appointment}', [ShopAppointmentController::class, 'show'])->name('appointments.show');
            Route::post('/appointments/{appointment}/mark-viewed', [ShopAppointmentController::class, 'markAsViewed'])->name('appointments.mark-viewed');
            Route::post('/appointments/{appointment}/reassign', [ShopAppointmentController::class, 'reassignEmployee'])->name('appointments.reassign');
            Route::post('/appointments/{appointment}/schedule-follow-up', [ShopAppointmentController::class, 'scheduleFollowUp'])->name('appointments.schedule-follow-up');
            Route::get('/payments', [ShopAppointmentController::class, 'payments'])->name('payments');
            Route::post('/mode/customer', [ShopDashboardController::class, 'switchToCustomerMode'])->name('mode.customer');
            
            // Reviews routes
            Route::get('/reviews', [ShopDashboardController::class, 'reviews'])->name('reviews');
            Route::post('/reviews/{rating}/comment', [ShopDashboardController::class, 'addComment'])->name('reviews.comment');
            
            // Employee routes with proper prefix and naming
            Route::prefix('employees')->name('employees.')->group(function () {
                Route::get('/', [ShopEmployeeController::class, 'index'])->name('index');
                Route::post('/', [ShopEmployeeController::class, 'store'])->name('store');
                Route::get('/{employee}', [ShopEmployeeController::class, 'show'])->name('show');
                Route::put('/{employee}', [ShopEmployeeController::class, 'update'])->name('update');
                Route::delete('/{employee}', [ShopEmployeeController::class, 'destroy'])->name('destroy');
                Route::post('/{employee}/restore', [ShopEmployeeController::class, 'restore'])->name('restore');
                
                // Employee analytics routes
                Route::post('/analytics', [ShopEmployeeController::class, 'analytics'])->name('analytics');
                Route::post('/{employee}/detailed-stats', [ShopEmployeeController::class, 'detailedStats'])->name('detailed-stats');
                
                // Employee availability routes
                Route::get('/{employee}/availability', [ShopEmployeeController::class, 'getAvailability'])->name('availability');
                Route::put('/{employee}/availability', [ShopEmployeeController::class, 'updateAvailability'])->name('update-availability');
            });
            
            // Services management routes
            Route::get('/services', [ShopServicesController::class, 'index'])->name('services');
            Route::get('/services/{service}', [ShopServicesController::class, 'show'])->name('services.show');
            Route::post('/services', [ShopServicesController::class, 'store'])->name('services.store');
            Route::put('/services/{service}', [ShopServicesController::class, 'update'])->name('services.update');
            Route::delete('/services/{service}', [ShopServicesController::class, 'destroy'])->name('services.destroy');
            Route::put('/services/{service}/status', [ShopServicesController::class, 'updateStatus'])->name('services.update-status');
            Route::post('/services/{service}/discounts', [ShopServicesController::class, 'addDiscount'])->name('services.add-discount');
            
            // Static routes
            Route::get('/analytics', [ShopAnalyticsController::class, 'index'])->name('analytics');
            Route::view('/settings', 'shop.settings.index')->name('settings');
            Route::post('/gallery', [ShopProfileController::class, 'uploadGalleryPhoto'])->name('gallery.upload');
            Route::delete('/gallery/{photo}', [ShopProfileController::class, 'deleteGalleryPhoto'])->name('gallery.delete');
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
            Route::get('/', [PetController::class, 'index'])->name('index');
            Route::get('/dashboard', [ProfileController::class, 'showPetDashboard'])->name('dashboard');
            Route::post('/', [PetController::class, 'store'])->name('store');
            Route::put('/{pet}', [PetController::class, 'update'])->name('update');
            Route::delete('/{pet}', [ProfileController::class, 'deletePet'])->name('delete');
            Route::post('/{pet}/update-photo', [ProfileController::class, 'updatePetPhoto'])->name('update-photo');
            Route::post('/{pet}/mark-deceased', [PetController::class, 'markDeceased'])->name('mark-deceased');
            
            // Pet details and health records - consolidated in PetController
            Route::get('/{pet}/details', [PetController::class, 'show'])->name('details');
            Route::get('/{pet}/health-record', [PetController::class, 'showHealthRecord'])->name('health-record');
            Route::get('/{pet}/add-health-record', [PetController::class, 'createHealthRecord'])->name('add-health-record');
            Route::get('/{pet}/user-add-health-record', [PetController::class, 'showUserAddHealthRecord'])->name('user-add-health-record');
            
            // New health record routes
            Route::post('/{pet}/vaccination', [PetController::class, 'storeVaccination'])->name('vaccination.store');
            Route::post('/{pet}/parasite-control', [PetController::class, 'storeParasiteControl'])->name('parasite-control.store');
            Route::post('/{pet}/health-issue', [PetController::class, 'storeHealthIssue'])->name('health-issue.store');
            Route::patch('/{pet}/health-issue/{issue}', [PetController::class, 'updateHealthIssue'])->name('update-health-issue');
        });
    });

    // Pet Profile Routes
    Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
        Route::get('/pets/{pet}', [PetController::class, 'show'])->name('pets.show');
        Route::post('/pets/{pet}/update-photo', [PetController::class, 'updatePhoto'])->name('pets.update-photo');
        Route::post('/pets/{pet}/mark-deceased', [PetController::class, 'markDeceased'])->name('pets.mark-deceased');
        Route::get('/pets/{pet}/health-record', [PetController::class, 'showHealthRecord'])->name('pets.health-record');
        Route::get('/pets/{pet}/add-health-record', [PetController::class, 'createHealthRecord'])->name('pets.add-health-record');
        
        // New grooming reminder routes
        Route::get('/pets/{pet}/grooming-status', [PetController::class, 'checkGroomingStatus'])->name('pets.grooming-status');
        Route::post('/pets/{pet}/grooming-preference', [PetController::class, 'updateGroomingPreference'])->name('pets.grooming-preference');
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
            Route::get('/acknowledgement', [BookingController::class, 'downloadAcknowledgement'])->name('acknowledgement.download');
            Route::post('/available-employees', [BookingController::class, 'getAvailableEmployees'])
                ->name('available-employees');
            Route::post('/validate-discount/{code}', [BookingController::class, 'validateDiscount'])
                ->name('validate-discount');
        });
    });

    // Appointment routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::get('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::put('/appointments/{appointment}/update-schedule', [AppointmentController::class, 'updateSchedule'])->name('appointments.update-schedule');
        Route::post('/appointments/{appointment}/accept', [AppointmentController::class, 'accept'])->name('appointments.accept');
        Route::post('/appointments/{appointment}/mark-as-paid', [AppointmentController::class, 'markAsPaid'])->name('appointments.mark-as-paid');
        Route::post('/appointments/{appointment}/shop-cancel', [AppointmentController::class, 'shopCancel'])->name('appointments.shop-cancel');
        Route::get('/appointments/{appointment}/note', [AppointmentController::class, 'getNote'])->name('appointments.get-note');
        Route::get('/appointments/{appointment}/download-receipt', [AppointmentController::class, 'downloadReceipt'])->name('appointments.download-receipt');
        
        // New rating routes
        Route::get('/appointments/{appointment}/rate', [RatingController::class, 'show'])->name('appointments.rate.show');
        Route::post('/appointments/{appointment}/rate', [RatingController::class, 'store'])->name('appointments.rate');
    });

    // Appointment Reschedule Routes
    Route::middleware(['auth', 'has-shop'])->group(function () {
        Route::post('/appointments/reschedule/{appointment}/approve', [AppointmentController::class, 'approveReschedule'])
            ->name('appointments.reschedule.approve');
        Route::post('/appointments/reschedule/{appointment}/decline', [AppointmentController::class, 'declineReschedule'])
            ->name('appointments.reschedule.decline');
        // Add route for marking reschedule requests as viewed
        Route::post('/appointments/{appointment}/mark-reschedule-viewed', [ShopAppointmentController::class, 'markRescheduleAsViewed'])
            ->name('appointments.mark-reschedule-viewed');
        // Add cancellation request routes
        Route::post('/appointments/cancellation/{appointment}/approve', [AppointmentController::class, 'approveCancellation'])
            ->name('appointments.cancellation.approve');
        Route::post('/appointments/cancellation/{appointment}/decline', [AppointmentController::class, 'declineCancellation'])
            ->name('appointments.cancellation.decline');
    });

    // Other customer routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/update-email', [SettingsController::class, 'updateEmail'])->name('settings.update-email');
    Route::put('/settings/update-password', [SettingsController::class, 'updatePassword'])->name('settings.update-password');
    Route::delete('/settings/delete-account', [SettingsController::class, 'deleteAccount'])->name('settings.delete-account');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{shop}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites/{shop}/check', [FavoriteController::class, 'check'])->name('favorites.check');
    Route::post('/shops/{shop}/review', [ShopController::class, 'submitReview'])->name('shops.review')->middleware('auth');

    // Notifications Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    
    // Add route for checking upcoming appointments
    Route::get('/appointments/check-upcoming', [AppointmentController::class, 'checkUpcomingAppointments'])
        ->name('appointments.checkUpcoming');

    // User report appeal routes
    Route::get('/user/report/{report}/appeal', [AppealController::class, 'showUserAppealForm'])->name('user.report.appeal.form');
    Route::post('/user/report/{report}/appeal', [AppealController::class, 'submitUserAppeal'])->name('user.report.appeal.submit');

    // Shop Appointment Actions
    Route::get('/appointments/{appointment}/accept', [ShopAppointmentController::class, 'accept'])->name('shop.appointments.accept');
    Route::post('/appointments/{appointment}/cancel', [ShopAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/mark-paid', [ShopAppointmentController::class, 'markAsPaid'])->name('appointments.mark-paid');
    Route::post('/appointments/{appointment}/reassign', [ShopAppointmentController::class, 'reassign'])->name('appointments.reassign');
    Route::post('/appointments/{appointment}/report', [ShopAppointmentController::class, 'submitReport'])->name('appointments.report');

    // Follow-up Appointment Routes
    Route::get('/appointments/{appointment}/follow-up', [ShopAppointmentController::class, 'showFollowUpForm'])->name('appointments.follow-up-form');
    Route::post('/appointments/{appointment}/schedule-follow-up', [ShopAppointmentController::class, 'scheduleFollowUp'])->name('appointments.schedule-follow-up');

    // Add appointment note route - fixed name to match what's used in the template
    Route::post('/appointments/{appointment}/add-note', [ShopAppointmentController::class, 'addNote'])->name('shop.appointments.add-note');
});

// Admin routes
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Shop management routes
    Route::get('/shops', [AdminController::class, 'shops'])->name('shops');
    Route::get('/shops/{shop}', [AdminController::class, 'show'])->name('shops.show');
    Route::get('/shops/{shop}/edit', [AdminDashboardController::class, 'editShop'])->name('shops.edit');
    Route::put('/shops/{shop}', [AdminDashboardController::class, 'updateShop'])->name('shops.update');
    Route::post('/shops/{shop}/toggle-status', [AdminDashboardController::class, 'toggleShopStatus'])->name('shops.toggle-status');
    Route::post('/shops/{shop}/approve', [AdminController::class, 'approveShop'])->name('shops.approve');
    Route::post('/shops/{shop}/reject', [AdminController::class, 'rejectShop'])->name('shops.reject');
    Route::get('/shops/{shop}/analytics', [AdminController::class, 'getShopAnalytics'])->name('shops.analytics');
    Route::get('/shops/{shop}/details', [AdminController::class, 'getShopDetails'])->name('shops.details');
    Route::get('/shops/{shop}/registration-details', [AdminController::class, 'getRegistrationDetails'])->name('shops.registration-details');
    
    // Add shop reports routes
    Route::get('/support', [App\Http\Controllers\Admin\ReportsController::class, 'shopReports'])->name('support');
    Route::get('/reports/{id}', [App\Http\Controllers\Admin\ReportsController::class, 'getReport'])->name('reports.get');
    Route::put('/reports/{id}/status', [App\Http\Controllers\Admin\ReportsController::class, 'updateReportStatus'])->name('reports.status.update');
    Route::post('/reports/{id}/status', [App\Http\Controllers\Admin\ReportsController::class, 'updateReportStatus'])->name('reports.update-status');
    // Add route for sending notifications to shop reports
    Route::post('/reports/{id}/send-notification', [App\Http\Controllers\Admin\ReportsController::class, 'sendNotificationToShop'])->name('reports.send-notification');
    
    // Add user reports routes
    Route::get('/user-reports/{id}', [App\Http\Controllers\Admin\ReportsController::class, 'getUserReport'])->name('user-reports.get');
    Route::put('/user-reports/{id}/status', [App\Http\Controllers\Admin\ReportsController::class, 'updateUserReportStatus'])->name('user-reports.status.update');
    Route::post('/user-reports/{id}/status', [App\Http\Controllers\Admin\ReportsController::class, 'updateUserReportStatus'])->name('reports.update-user-status');
    // Add route for sending notifications to user reports
    Route::post('/user-reports/{id}/send-notification', [App\Http\Controllers\Admin\ReportsController::class, 'sendNotificationToUser'])->name('user-reports.send-notification');

    // Test route for debugging user reports
    Route::get('/test-user-report/{id}', function($id) {
        $report = \App\Models\UserReport::with(['reporter', 'reportedUser'])->find($id);
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }
        
        return response()->json([
            'report' => $report,
            'has_reporter' => $report->reporter !== null,
            'has_reported_user' => $report->reportedUser !== null,
            'reporter_name' => $report->reporter ? $report->reporter->name : null,
            'reported_user_name' => $report->reportedUser ? $report->reportedUser->name : null
        ]);
    });

    // User management routes
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/users/{user}/activity', [AdminController::class, 'getUserActivity'])->name('users.activity');
    Route::get('/users/{user}/complaints', [AdminController::class, 'getUserComplaints'])->name('users.complaints');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');

    // Services management routes
    Route::get('/services', [AdminServicesController::class, 'index'])->name('services');
    Route::get('/services/{service}', [AdminServicesController::class, 'show'])->name('services.show');
    Route::post('/services/{service}/status', [AdminServicesController::class, 'updateStatus'])->name('services.update-status');
    Route::delete('/services/{service}', [AdminServicesController::class, 'destroy'])->name('services.destroy');

    // Payment management routes
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments');
    Route::post('/payments/{subscription}/verify', [App\Http\Controllers\Admin\PaymentController::class, 'verifyPayment'])->name('payments.verify');
    Route::post('/payments/{subscription}/reject', [App\Http\Controllers\Admin\PaymentController::class, 'rejectPayment'])->name('payments.reject');
    Route::post('/payments/update-rate', [App\Http\Controllers\Admin\PaymentController::class, 'updateSubscriptionRate'])->name('payments.update-rate');
    Route::get('/payments/{subscription}/details', [App\Http\Controllers\Admin\PaymentController::class, 'getPaymentDetails'])->name('payments.details');
    
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');

    // Test notifications (for development purposes)
    Route::get('/test-notifications', function () {
        return view('admin.test-notifications');
    })->name('test-notifications');

    // Appeals routes - using different formats to ensure one works
    Route::get('/appeals/shop/{report}', [App\Http\Controllers\Admin\AppealsController::class, 'showShopAppeal'])->name('appeals.shop');
    Route::get('/appeals/user/{report}', [App\Http\Controllers\Admin\AppealsController::class, 'showUserAppeal'])->name('appeals.user');
    Route::put('/appeals/{appeal}/status', [App\Http\Controllers\Admin\AppealsController::class, 'updateAppealStatus'])->name('appeals.update');
    
    // API route for fetching appeal details for the modal
    Route::get('/appeals/details/{appeal}', [App\Http\Controllers\Admin\AppealsController::class, 'getAppealDetails'])->name('appeals.details');
    
    // Alternative appeal routes that exactly match the expected names in the view
    Route::get('/alt-appeals/shop/{report}', [App\Http\Controllers\Admin\AppealsController::class, 'showShopAppeal'])->name('admin.appeals.shop');
    Route::get('/alt-appeals/user/{report}', [App\Http\Controllers\Admin\AppealsController::class, 'showUserAppeal'])->name('admin.appeals.user');
    Route::put('/alt-appeals/{appeal}/status', [App\Http\Controllers\Admin\AppealsController::class, 'updateAppealStatus'])->name('admin.appeals.update');
});

Route::middleware(['auth', 'has-shop'])->group(function () {
    Route::post('/shop/services', [ShopServicesController::class, 'store']);
    Route::put('/shop/services/{service}', [ShopServicesController::class, 'update']);
    Route::put('/shop/services/{service}/status', [ShopServicesController::class, 'updateStatus']);
    Route::delete('/shop/services/{service}', [ShopServicesController::class, 'destroy']);
    Route::post('/shop/services/{service}/discounts', [ShopServicesController::class, 'storeDiscount']);
    
    // Add analytics route
    Route::get('/shop/analytics', [ShopAnalyticsController::class, 'index'])->name('shop.analytics');
    Route::get('/shop/analytics/export/{type}', [ShopAnalyticsController::class, 'export'])->name('shop.analytics.export');
});

// Shop Settings Routes
Route::middleware(['auth', 'verified', 'has-shop'])->group(function () {
    Route::prefix('shop/settings')->name('shop.settings.')->group(function () {
        Route::post('/profile', [ShopSettingsController::class, 'updateProfile'])->name('profile.update');
        Route::post('/business', [ShopSettingsController::class, 'updateBusiness'])->name('business.update');
        Route::post('/services', [ShopSettingsController::class, 'updateServices'])->name('services.update');
        Route::post('/hours', [ShopSettingsController::class, 'updateHours'])->name('hours.update');
        Route::post('/notifications', [ShopSettingsController::class, 'updateNotifications'])->name('notifications.update');
    });
});

// Admin Reports Routes
Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/admin/reports', [App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('admin.reports');
    Route::get('/admin/reports/filter', [App\Http\Controllers\Admin\ReportsController::class, 'filter'])->name('admin.reports.filter');
    Route::get('/admin/reports/export/{format}', [App\Http\Controllers\Admin\ReportsController::class, 'export'])->name('admin.reports.export');
});

// Shop Reports Routes
Route::middleware(['auth', 'has-shop'])->group(function () {
    Route::get('/shop/reports', [ShopReportController::class, 'index'])->name('shop.reports');
    Route::get('/shop/reports/filter', [ShopReportController::class, 'filter'])->name('shop.reports.filter');
    Route::get('/shop/reports/export', [ShopReportController::class, 'export'])->name('shop.reports.export');
    Route::get('/shop/reports/print', [ShopReportController::class, 'print'])->name('shop.reports.print');
});

// Post route for submitting shop reports - accessible to all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::post('/shop/report', [ShopReportController::class, 'store'])->name('shop.report.store');
});

// Post route for submitting user reports - accessible to shop owners/employees
Route::middleware(['auth'])->group(function () {
    Route::post('/user/report', [UserReportController::class, 'store'])->name('user.report.store');
});

// Shop Admin Routes
Route::middleware(['auth', 'has-shop'])->prefix('shop')->name('shop.')->group(function () {
    // Existing routes
    Route::get('/dashboard', [App\Http\Controllers\Shop\DashboardController::class, 'index'])->name('dashboard');
    
    // Add analytics routes
    Route::get('/analytics', [App\Http\Controllers\Shop\AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/export/{type}', [App\Http\Controllers\Shop\AnalyticsController::class, 'export'])->name('analytics.export');
    
    // Add appointment note route
    Route::post('/appointments/{appointment}/add-note', [ShopAppointmentController::class, 'addNote'])->name('shop.appointments.add-note');
    
    // Other routes
});

// Add service lookup route
Route::get('/service-lookup', function (Request $request) {
    $shopId = $request->query('shop_id');
    $serviceType = $request->query('service_type');
    
    if (!$shopId || !$serviceType) {
        return response()->json(['error' => 'Missing shop_id or service_type parameters'], 400);
    }
    
    try {
        // Find a service matching the type in the specified shop
        $service = \App\Models\Service::where('shop_id', $shopId)
            ->where(function($query) use ($serviceType) {
                // Only search in columns that actually exist in the database
                $query->where('name', 'like', "%{$serviceType}%")
                    ->orWhere('description', 'like', "%{$serviceType}%");
            })
            ->where('status', 'active')
            ->first();
        
        if ($service) {
            return response()->json([
                'success' => true,
                'service_id' => $service->id,
                'service_name' => $service->name
            ]);
        }
        
        // If no exact match, return the first active service as fallback
        $fallbackService = \App\Models\Service::where('shop_id', $shopId)
            ->where('status', 'active')
            ->first();
            
        if ($fallbackService) {
            return response()->json([
                'success' => true,
                'service_id' => $fallbackService->id,
                'service_name' => $fallbackService->name,
                'fallback' => true
            ]);
        }
        
        return response()->json(['error' => 'No matching service found'], 404);
    } catch (\Exception $e) {
        \Log::error('Service lookup error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to look up service'], 500);
    }
});

// Debug routes - only available in local environment
if (app()->environment('local')) {
    Route::get('/debug/email-test', function () {
        $email = request('email', auth()->user()->email ?? env('MAIL_FROM_ADDRESS'));
        $result = App\Services\NotificationEmailService::debugEmailConfiguration($email);
        return response()->json($result);
    })->middleware(['auth']);
}
