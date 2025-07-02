<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::with(['tickets', 'agency'])->get();
    }

    public function show(User $user)
    {
        return $user->load(['tickets', 'agency', 'unreadNotifications']);
    }

    public function markNotificationAsRead(Request $request, User $user, $notificationId)
    {
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    public function markAllNotificationsAsRead(User $user)
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['message' => 'Toutes les notifications marquées comme lues']);
    }

    public function deleteNotification(Request $request, User $user, $notificationId)
    {
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->delete();
        return response()->json(['message' => 'Notification supprimée']);
    }

    public function statistics(User $user)
    {
        return $this->statisticsService->getUserStatistics($user);
            ->withCount(['tickets' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->withCount(['tickets as completed_tickets' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->withCount(['tickets as today_tickets' => function ($query) {
                $query->whereDate('created_at', today());
            }])
            ->get()
        ];

        return $data;
    }
}
