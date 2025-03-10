<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get user's notifications.
     */
    public function index()
    {
        $query = auth()->user()->notifications();
        
        // Filter by type if provided
        if (request()->has('type')) {
            $query->where('type', request('type'));
        }
        
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $unreadQuery = auth()->user()->unreadNotifications();
        
        // Filter unread count by type if provided
        if (request()->has('type')) {
            $unreadQuery->where('type', request('type'));
        }
        
        $unreadCount = $unreadQuery->count();

        if (request()->wantsJson()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);
        
        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Notification marked as read',
                'unread_count' => auth()->user()->unreadNotifications()->count()
            ]);
        }

        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $user->notifications()->unread()->update([
            'status' => 'read',
            'read_at' => now()
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'All notifications marked as read',
                'unread_count' => 0
            ]);
        }

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        
        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Notification deleted',
                'unread_count' => auth()->user()->unreadNotifications()->count()
            ]);
        }

        return back()->with('success', 'Notification deleted');
    }
}
