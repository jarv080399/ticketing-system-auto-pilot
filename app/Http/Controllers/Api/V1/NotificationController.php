<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'data' => $user->notifications()->limit(20)->get(),
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function preferences(Request $request)
    {
        return response()->json([
            'data' => $request->user()->notificationPreferences,
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'preferences' => 'required|array',
            'preferences.*.id' => 'required|exists:notification_preferences,id',
            'preferences.*.is_enabled' => 'required|boolean',
        ]);

        foreach ($request->preferences as $pref) {
            \App\Models\NotificationPreference::where('id', $pref['id'])
                ->where('user_id', $request->user()->id)
                ->update(['is_enabled' => $pref['is_enabled']]);
        }

        return response()->json(['message' => 'Preferences updated']);
    }
}
