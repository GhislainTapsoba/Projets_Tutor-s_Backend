<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index()
    {
        $stats = $this->statisticsService->getSystemStatistics();

        // Ajouter des statistiques supplémentaires
        $stats['agencies'] = Agency::select('id', 'name', 'district')
            ->with(['tickets' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }])
            ->get();

        $stats['events'] = Event::select('id', 'name', 'start_date', 'end_date')
            ->with(['tickets' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }])
            ->get();

        $stats['users'] = User::select('id', 'name', 'email', 'role')
            ->with(['tickets' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }])
            ->get();

        return $stats;
    }

    public function agencyStatistics(Agency $agency)
    {
        return $this->statisticsService->getAgencyStatistics($agency);
    }

    public function eventStatistics(Event $event)
    {
        return $this->statisticsService->getEventStatistics($event);
    }

    public function userStatistics(User $user)
    {
        return $this->statisticsService->getUserStatistics($user);
    }
}
