<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class ShopAppointmentController extends Controller
{
    public function index()
    {
        $shop = auth()->user()->shop;
        $appointments = $shop->appointments()
            ->with(['user', 'pet'])
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->groupBy(function($appointment) {
                return $appointment->appointment_date->format('Y-m-d');
            });

        return view('shop.appointments.index', compact('appointments'));
    }
} 