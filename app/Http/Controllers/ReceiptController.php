<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    public function download(Shop $shop)
    {
        try {
            $user = auth()->user();
            $booking_details = session('booking_details');
            
            if (!$booking_details) {
                return redirect()->route('home')
                    ->with('error', 'Booking details not found');
            }

            $receipt_number = 'RCP-' . date('Ymd') . '-' . rand(1000, 9999);

            $data = [
                'shop' => $shop,
                'user' => $user,
                'booking_details' => $booking_details,
                'receipt_number' => $receipt_number
            ];

            $pdf = Pdf::loadView('pdfs.receipt', $data);

            return $pdf->download('receipt-' . $receipt_number . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error generating receipt: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate receipt. Please try again.');
        }
    }
} 