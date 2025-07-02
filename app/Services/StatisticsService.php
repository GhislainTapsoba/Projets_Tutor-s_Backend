<?php

namespace App\Services;

use App\Models\Models\Agency;
use App\Models\Models\Event;
use App\Models\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getAgencyStatistics(Agency $agency)
    {
        return [
            'total_tickets' => $agency->tickets()->count(),
            'active_tickets' => $agency->tickets()->where('status', 'pending')->count(),
            'completed_tickets' => $agency->tickets()->where('status', 'completed')->count(),
            'average_wait_time' => $agency->tickets()
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, start_time, end_time)')),
            'total_revenue' => $agency->events()->sum('price'),
            'active_events' => $agency->events()->where('end_date', '>', now())->count(),
            'total_events' => $agency->events()->count(),
            'agents_count' => $agency->users()->where('role', 'agent')->count(),
            'clients_count' => $agency->users()->where('role', 'client')->count(),
        ];
    }

    public function getUserStatistics(User $user)
    {
        return [
            'total_tickets' => $user->tickets()->count(),
            'active_tickets' => $user->tickets()->where('status', 'pending')->count(),
            'completed_tickets' => $user->tickets()->where('status', 'completed')->count(),
            'average_wait_time' => $user->tickets()
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, start_time, end_time)')),
            'unread_notifications' => $user->unreadNotifications()->count(),
            'total_notifications' => $user->notifications()->count(),
        ];
    }

    public function getEventStatistics(Event $event)
    {
        return [
            'total_tickets' => $event->tickets()->count(),
            'active_tickets' => $event->tickets()->where('status', 'pending')->count(),
            'completed_tickets' => $event->tickets()->where('status', 'completed')->count(),
            'average_wait_time' => $event->tickets()
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, start_time, end_time)')),
            'total_revenue' => $event->tickets()->sum('price'),
            'participants_count' => $event->tickets()->distinct('user_id')->count('user_id'),
        ];
    }

    public function getSystemStatistics()
    {
        return [
            'total_agencies' => Agency::count(),
            'active_agencies' => Agency::where('is_active', true)->count(),
            'total_events' => Event::count(),
            'active_events' => Event::where('end_date', '>', now())->count(),
            'total_tickets' => Ticket::count(),
            'active_tickets' => Ticket::where('status', 'pending')->count(),
            'completed_tickets' => Ticket::where('status', 'completed')->count(),
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('last_login_at')->count(),
            'total_revenue' => Event::sum('price'),
            'average_wait_time' => Ticket::whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, start_time, end_time)')),
        ];
    }
}
