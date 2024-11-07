<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/dashboard', function () {
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

Route::get('/addemployee', function () {
    return view('addemployee');
})->name('addemployee');

Route::get('/account', function () {
    return view('account');
})->name('account');

Route::get('/shopprofile', function () {
    return view('shopprofile');
})->name('shopprofile');

Route::get('/addapointment', function () {
    return view('addapointment');
})->name('addapointment');

Route::get('/addservice', function () {
    return view('addservice');
})->name('addservice');
