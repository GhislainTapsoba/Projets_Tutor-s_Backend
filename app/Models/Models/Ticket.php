<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Models\Event;
use App\Models\Models\User;

class Ticket extends Model
{
    use HasFactory;

    protected $factory = \Database\Factories\TicketFactory::class;

    protected $fillable = [
        'event_id',
        'user_id',
        'reference',
        'price',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifyStatusChange($previousStatus)
    {
        $statusMessages = [
            'pending' => 'Votre ticket est en attente de traitement',
            'in_progress' => 'Votre ticket est en cours de traitement',
            'completed' => 'Votre ticket a été traité avec succès',
            'cancelled' => 'Votre ticket a été annulé'
        ];

        if (isset($statusMessages[$this->status])) {
            $message = $statusMessages[$this->status];
            if ($previousStatus) {
                $message .= ' (précédemment ' . $statusMessages[$previousStatus] . ')';
            }
            
            $this->user->notify(new TicketNotification($this, $message));
        }
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getWaitTimeAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->end_time->diffInMinutes($this->start_time);
        }
        return null;
    }

    public function getRemainingTimeAttribute()
    {
        if ($this->estimated_time && $this->status === 'pending') {
            return $this->estimated_time->diffInMinutes(now());
        }
        return null;
    }
}
