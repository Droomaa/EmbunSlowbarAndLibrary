<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Fetch notifications for the authenticated user
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->take(50) // Limit to latest 50
                            ->get();
        return response()->json(['data' => $notifications]);
    }

    // Mark a specific notification as read
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->find($id);
        if ($notification) {
            $notification->is_read = true;
            $notification->save();
            return response()->json(['message' => 'Notification marked as read']);
        }
        return response()->json(['message' => 'Notification not found'], 404);
    }

    // Mark all notifications as read for the user
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return response()->json(['message' => 'All notifications marked as read']);
    }
}
