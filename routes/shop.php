<?php

use App\Http\Controllers\ShopEmployeeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'has-shop'])->prefix('shop')->group(function () {
    // Schedule routes
    Route::prefix('schedule')->name('shop.schedule.')->group(function () {
        // Events
        Route::get('/events', [ShopEmployeeController::class, 'getEvents'])->name('events');
        Route::post('/events', [ShopEmployeeController::class, 'storeEvent'])->name('events.store');
        Route::put('/events/{event}', [ShopEmployeeController::class, 'updateEvent'])->name('events.update');
        Route::delete('/events/{event}', [ShopEmployeeController::class, 'deleteEvent'])->name('events.delete');
        
        // Time Off
        Route::get('/time-off', [ShopEmployeeController::class, 'getTimeOff'])->name('time-off.index');
        Route::post('/time-off', [ShopEmployeeController::class, 'storeTimeOff'])->name('time-off.store');
        Route::delete('/time-off/{id}', [ShopEmployeeController::class, 'deleteTimeOff'])->name('time-off.delete');
    });
}); 