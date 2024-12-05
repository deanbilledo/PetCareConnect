<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Appointment;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    public function download(Appointment $appointment)
    {
        // Check if appointment is accepted
        if ($appointment->status !== 'accepted') {
            return back()->with('error', 'Receipt is only available for accepted appointments.');
        }

        // Check if user is authorized to download this receipt
        if (auth()->id() !== $appointment->user_id && auth()->id() !== $appointment->shop->user_id) {
            return back()->with('error', 'Unauthorized to download this receipt.');
        }

        // Generate PDF
        $pdf = PDF::loadView('receipts.receipt', [
            'appointment' => $appointment,
            'shop' => $appointment->shop,
            'service' => $appointment->service,
            'pet' => $appointment->pet,
            'user' => $appointment->user
        ]);

        // Generate filename
        $filename = 'receipt_' . $appointment->id . '_' . date('Y-m-d') . '.pdf';

        // Return the PDF for download
        return $pdf->download($filename);
    }

    public function downloadAcknowledgement(Shop $shop)
    {
        try {
            $user = auth()->user();
            $booking_details = session('booking_details');
            
            if (!$booking_details) {
                return redirect()->route('home')
                    ->with('error', 'Booking details not found');
            }

            // Generate PDF
            $pdf = PDF::loadView('pdfs.acknowledgement', [
                'shop' => $shop,
                'user' => $user,
                'booking_details' => $booking_details
            ]);

            // Generate filename
            $filename = 'acknowledgement_' . date('Ymd') . '_' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error generating acknowledgement: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate acknowledgement. Please try again.');
        }
    }
} 