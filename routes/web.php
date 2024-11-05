<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');


Route::get('/appointments', function () {
    return view('appointments');
})->name('appointments');

Route::get('/employees', function () {
    return view('employees');
})->name('employees');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/billingandpayments', function () {
    return view('billingandpayments');
})->name('billingandpayments');

Route::get('/analytics', function () {
    return view('analytics');
})->name('analytics');

Route::get('/customers', function () {
    return view('customers');
})->name('customers');