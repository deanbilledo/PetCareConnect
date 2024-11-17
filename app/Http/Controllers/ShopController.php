<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        return view('groomVetLandingPage.petlandingpage');
    }

    public function groomingShops()
    {
        // Add logic for grooming shops page
        return view('shops.grooming');
    }
} 