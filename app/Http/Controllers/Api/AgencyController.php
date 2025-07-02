<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function index()
    {
        return Agency::with('tickets')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        return Agency::create($validated);
    }

    public function show(Agency $agency)
    {
        return $agency->load('tickets');
    }

    public function update(Request $request, Agency $agency)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'district' => 'string|max:255',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'address' => 'string',
            'phone' => 'string|max:20',
            'email' => 'string|email',
            'description' => 'string',
            'is_active' => 'boolean'
        ]);

        $agency->update($validated);

        // Notifier les agents de la mise à jour
        $agency->notifyAgents('L\'agence "' . $agency->name . '" a été mise à jour.');

        return $agency;
    }

    public function destroy(Agency $agency)
    {
        // Notifier les agents de la suppression
        $agency->notifyAgents('L\'agence "' . $agency->name . '" a été supprimée.', 'agency_deleted');

        $agency->delete();
        return response()->noContent();
    }

    public function nearest(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if (!$latitude || !$longitude) {
            return response()->json(['error' => 'Latitude and longitude are required'], 400);
        }

        $agencies = Agency::where('is_active', true)
            ->get()
            ->map(function ($agency) use ($latitude, $longitude) {
                $agency->distance = $agency->calculateDistance($latitude, $longitude);
                return $agency;
            })
            ->sortBy('distance')
            ->values();

        return $agencies;
    }

    public function statistics(Agency $agency)
    {
        return $this->statisticsService->getAgencyStatistics($agency);
                ->with(['tickets' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(5);
                }])
                ->get()
        ];

        return $data;
    }
}
