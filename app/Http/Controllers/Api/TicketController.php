<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index()
    {
        return Ticket::with(['agency', 'user'])->orderBy('created_at', 'desc')->get();
    }

    public function statistics()
    {
        $data = [
            'total_tickets' => Ticket::count(),
            'today_tickets' => Ticket::whereDate('created_at', today())->count(),
            'pending_tickets' => Ticket::where('status', 'pending')->count(),
            'in_progress_tickets' => Ticket::where('status', 'in_progress')->count(),
            'completed_tickets' => Ticket::where('status', 'completed')->count(),
            'average_wait_time' => Ticket::whereNotNull('end_time')
                ->whereNotNull('start_time')
                ->avg('wait_time'),
            'tickets_by_agency' => Ticket::select('agency_id', DB::raw('count(*) as total'))
                ->groupBy('agency_id')
                ->get()
                ->map(function ($item) {
                    $agency = Agency::find($item->agency_id);
                    return [
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
            'agency_id' => 'required|exists:agencies,id',
            'user_id' => 'required|exists:users,id',
            'estimated_time' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        $validated['reference'] = 'TICKET-' . time() . '-' . rand(1000, 9999);
        
        // Calculer le temps estimé basé sur la file d'attente
        $agency = Agency::findOrFail($validated['agency_id']);
        $pendingTickets = Ticket::where('agency_id', $validated['agency_id'])
            ->where('status', 'pending')
            ->count();

        $estimatedTime = now()->addMinutes($pendingTickets * 10); // 10 minutes par ticket
        $validated['estimated_time'] = $estimatedTime;

        return Ticket::create($validated);
    }

    public function show(Ticket $ticket)
    {
        return $ticket->load('event', 'user');
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'agency_id' => 'exists:agencies,id',
            'user_id' => 'exists:users,id',
            'estimated_time' => 'nullable|date',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'status' => 'in:pending,in_progress,completed,cancelled'
        ]);

        if ($validated['status'] === 'in_progress' && !$ticket->start_time) {
            $validated['start_time'] = now();
        }

        if ($validated['status'] === 'completed' && !$ticket->end_time) {
            $validated['end_time'] = now();
        }

        $previousStatus = $ticket->status;
        $ticket->update($validated);

        // Notifier l'utilisateur du changement de statut
        $ticket->notifyStatusChange($previousStatus);

        return $ticket;
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->noContent();
    }
}
