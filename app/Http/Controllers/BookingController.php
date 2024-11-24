<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Pet;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
        $this->middleware('web');
    }

    public function show(Shop $shop)
    {
        $shop->load(['ratings.user' => function($query) {
            $query->select('id', 'first_name', 'last_name', 'profile_photo_path');
        }]);

        // Add detailed debugging
        foreach($shop->ratings as $rating) {
            \Log::info('Rating ID: ' . $rating->id);
            \Log::info('User ID: ' . $rating->user->id);
            \Log::info('User Name: ' . $rating->user->name);
            \Log::info('Profile Photo Path: ' . $rating->user->profile_photo_path);
            \Log::info('Profile Photo URL: ' . $rating->user->profile_photo_url);
            \Log::info('Storage Path: ' . storage_path('app/public/' . $rating->user->profile_photo_path));
            \Log::info('File Exists: ' . (Storage::disk('public')->exists($rating->user->profile_photo_path) ? 'Yes' : 'No'));
        }

        return view('booking.book', compact('shop'));
    }

    public function process(Shop $shop)
    {
        $pets = auth()->user()->pets;
        return view('booking.process', compact('shop', 'pets'));
    }

    public function selectService(Shop $shop, Request $request)
    {
        $request->validate([
            'appointment_type' => 'required|in:single,multiple',
            'pet_ids' => 'required|array',
            'pet_ids.*' => 'exists:pets,id'
        ]);

        $pets = Pet::whereIn('id', $request->pet_ids)->get();
        $services = $this->getServicesByShopType($shop);

        return view('booking.select-service', compact('shop', 'pets', 'services'));
    }

    public function selectDateTime(Shop $shop, Request $request)
    {
        $request->validate([
            'pet_ids' => 'required|array',
            'pet_ids.*' => 'exists:pets,id',
            'services' => 'required|array',
            'services.*' => 'required|string'
        ]);

        $availableSlots = $this->getAvailableTimeSlots($shop);

        return view('booking.select-datetime', compact('shop', 'availableSlots'));
    }

    public function confirm(Shop $shop, Request $request)
    {
        $request->validate([
            'pet_ids' => 'required|array',
            'pet_ids.*' => 'exists:pets,id',
            'services' => 'required|array',
            'services.*' => 'required|string',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required'
        ]);

        $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);
        
        $pets = Pet::whereIn('id', $request->pet_ids)->get();
        $services = $this->getServicesByShopType($shop);

        return view('booking.confirm', compact('shop', 'pets', 'services', 'appointmentDateTime'));
    }

    public function store(Shop $shop, Request $request)
    {
        $request->validate([
            'pet_ids' => 'required|array',
            'pet_ids.*' => 'exists:pets,id',
            'services' => 'required|array',
            'services.*' => 'required|string',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'notes' => 'nullable|string'
        ]);

        $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);

        try {
            foreach ($request->pet_ids as $index => $petId) {
                $appointment = Appointment::create([
                    'user_id' => auth()->id(),
                    'shop_id' => $shop->id,
                    'pet_id' => $petId,
                    'service_type' => $request->services[$index],
                    'service_price' => $this->getServicePrice($shop, $request->services[$index]),
                    'appointment_date' => $appointmentDateTime,
                    'notes' => $request->notes,
                    'status' => 'pending'
                ]);
                
                \Log::info('Created appointment:', $appointment->toArray());
            }

            // Store booking details in session for thank you page
            session()->flash('booking_details', [
                'shop_name' => $shop->name,
                'date' => $appointmentDateTime->format('F j, Y'),
                'time' => $appointmentDateTime->format('g:i A'),
            ]);

            return redirect()->route('booking.thank-you');
        } catch (\Exception $e) {
            \Log::error('Error creating appointment: ' . $e->getMessage());
            return back()->with('error', 'There was an error creating your appointment. Please try again.');
        }
    }

    private function getServicesByShopType(Shop $shop)
    {
        if ($shop->type === 'grooming') {
            return [
                'full_grooming' => [
                    'name' => 'Full Grooming Service',
                    'price' => 799,
                    'description' => 'Bath, Haircut, Nail Trimming, Ear Cleaning'
                ],
                'basic_bath' => [
                    'name' => 'Basic Bath Package',
                    'price' => 349,
                    'description' => 'Bath and Blow Dry'
                ],
                // Add more grooming services
            ];
        } else {
            return [
                'checkup' => [
                    'name' => 'General Check-up',
                    'price' => 299,
                    'description' => 'Complete Physical Examination'
                ],
                'vaccination' => [
                    'name' => 'Vaccination',
                    'price' => 899,
                    'description' => 'Core Vaccines Available'
                ],
                // Add more veterinary services
            ];
        }
    }

    private function getServicePrice(Shop $shop, string $serviceType)
    {
        $services = $this->getServicesByShopType($shop);
        return $services[$serviceType]['price'] ?? 0;
    }

    private function getAvailableTimeSlots(Shop $shop)
    {
        // This is a simplified version. You should implement proper time slot logic
        $slots = [];
        $start = Carbon::today()->setHour(8)->setMinute(30);
        $end = Carbon::today()->setHour(17)->setMinute(0);

        while ($start <= $end) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        return $slots;
    }
} 