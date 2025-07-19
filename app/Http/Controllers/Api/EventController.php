<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Agency;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class EventController extends Controller
{
    public function index()
    {
        return Event::with(['tickets' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->get();
    }

    public function statistics()
    {
        return $this->statisticsService->getStatistics();
                        'agency' => $agency,
                        'total' => $item->total
                    ];
                })
        ];

        return $data;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string',
            'image_url' => 'nullable|url',
            'agency_id' => 'nullable|exists:agencies,id'
        ]);

        $event = Event::create($validated);
        
        // Si aucune agence n'est spécifiée, créer une nouvelle agence
        if (!$validated['agency_id']) {
            $agency = Agency::create([
                'name' => $event->name,
                'district' => $event->location,
                'address' => $event->location,
                'is_active' => true
            ]);
            $event->agency_id = $agency->id;
            $event->save();
        }

        return $event->load('agency');
    }

    public function show(Event $event)
    {
        return $event->load('tickets');
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'capacity' => 'integer|min:1',
            'location' => 'string',
            'image_url' => 'url',
            'agency_id' => 'exists:agencies,id'
        ]);

        $event->update($validated);

        // Notifier les utilisateurs et les agents de la mise à jour
        $event->notifyUsers('L\'événement "' . $event->name . '" a été mis à jour.');
        $event->notifyAgents('L\'événement "' . $event->name . '" a été mis à jour.');

        return $event->load('agency');
    }

    public function destroy(Event $event)
    {
        // Notifier les utilisateurs et les agents de la suppression
        $event->notifyUsers('L\'événement "' . $event->name . '" a été supprimé.', 'event_deleted');
        $event->notifyAgents('L\'événement "' . $event->name . '" a été supprimé.', 'event_deleted');

        $event->delete();
        return response()->noContent();
    }
}
