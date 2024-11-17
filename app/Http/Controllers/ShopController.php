<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        return view('petlandingpage');
    }

    public function groomingShops()
    {
        // You can add logic here to fetch grooming shops
        return view('petlandingpage');
    }
} 