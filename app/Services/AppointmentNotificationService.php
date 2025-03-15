<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Shop;
use App\Models\User;

class AppointmentNotificationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Create a notification for a new appointment
     *
     * @param Appointment $appointment
     * @return void
     */
    public static function createNewAppointmentNotification(Appointment $appointment)
    {
        // Create notification for shop owner
        if ($appointment->shop && $appointment->shop->user) {
            self::notifyShopOwner($appointment);
        }

        // Create notification for customer
        if ($appointment->user) {
            self::notifyCustomer($appointment);
        }
    }

    /**
     * Notify the shop owner about the new appointment
     *
     * @param Appointment $appointment
     * @return void
     */
    private static function notifyShopOwner(Appointment $appointment)
    {
        $shopUser = $appointment->shop->user;
        $petName = $appointment->pet ? $appointment->pet->name : 'Pet';
        $customerName = $appointment->user ? $appointment->user->name : 'Customer';
        
        $shopUser->notifyWithEmail([
            'type' => 'appointment',
            'title' => 'New Appointment Request',
            'message' => "New appointment request for {$petName} from {$customerName} on " . $appointment->appointment_date->format('M d, Y \a\t g:i A'),
            'action_url' => route('shop.appointments', ['highlight' => $appointment->id]),
            'action_text' => 'View Appointment',
            'status' => 'unread',
            'icon' => 'appointment'
        ]);
    }

    /**
     * Notify the customer about their appointment
     *
     * @param Appointment $appointment
     * @return void
     */
    private static function notifyCustomer(Appointment $appointment)
    {
        $customer = $appointment->user;
        $shopName = $appointment->shop ? $appointment->shop->name : 'Shop';
        $petName = $appointment->pet ? $appointment->pet->name : 'Pet';
        
        $customer->notifyWithEmail([
            'type' => 'appointment',
            'title' => 'Appointment Submitted',
            'message' => "Your appointment for {$petName} at {$shopName} on " . $appointment->appointment_date->format('M d, Y \a\t g:i A') . " has been submitted.",
            'action_url' => route('appointments.show', $appointment),
            'action_text' => 'View Details',
            'status' => 'unread',
            'icon' => 'appointment'
        ]);
    }
    
    /**
     * Create a notification for an appointment status change
     *
     * @param Appointment $appointment
     * @param string $status
     * @return void
     */
    public static function createStatusChangeNotification(Appointment $appointment, string $status)
    {
        $customer = $appointment->user;
        $shopName = $appointment->shop ? $appointment->shop->name : 'Shop';
        $petName = $appointment->pet ? $appointment->pet->name : 'Pet';
        
        $title = '';
        $message = '';
        
        switch ($status) {
            case 'accepted':
                $title = 'Appointment Confirmed';
                $message = "Your appointment for {$petName} at {$shopName} on " . $appointment->appointment_date->format('M d, Y \a\t g:i A') . " has been confirmed.";
                break;
            case 'cancelled':
                $title = 'Appointment Cancelled';
                $message = "Your appointment for {$petName} at {$shopName} on " . $appointment->appointment_date->format('M d, Y \a\t g:i A') . " has been cancelled.";
                break;
            case 'completed':
                $title = 'Appointment Completed';
                $message = "Your appointment for {$petName} at {$shopName} on " . $appointment->appointment_date->format('M d, Y \a\t g:i A') . " has been marked as completed.";
                break;
            case 'reschedule_approved':
                $title = 'Reschedule Approved';
                $message = "Your request to reschedule the appointment for {$petName} at {$shopName} has been approved.";
                break;
            default:
                $title = 'Appointment Update';
                $message = "Your appointment for {$petName} at {$shopName} on " . $appointment->appointment_date->format('M d, Y \a\t g:i A') . " has been updated.";
        }
        
        $customer->notifyWithEmail([
            'type' => 'appointment',
            'title' => $title,
            'message' => $message,
            'action_url' => route('appointments.show', $appointment),
            'action_text' => 'View Details',
            'status' => 'unread',
            'icon' => 'appointment'
        ]);
    }
}
