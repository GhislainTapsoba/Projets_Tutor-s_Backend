<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Models\Ticket;

class Event extends Model
{
    use HasFactory;

    protected $factory = \Database\Factories\EventFactory::class;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'capacity',
        'location',
        'image_url',
        'agency_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class)->orderBy('created_at', 'desc');
    }

    public function notifyUsers($message, $type = 'event')
    {
        $users = User::whereHas('tickets', function ($query) {
            $query->where('event_id', $this->id);
        })->get();

        foreach ($users as $user) {
            $user->notify(new EventNotification($this, $message, $type));
        }
    }

    public function notifyAgents($message, $type = 'event')
    {
        $agents = User::agents()->get();
        foreach ($agents as $agent) {
            $agent->notify(new EventNotification($this, $message, $type));
        }
    }

    public function getActiveTicketsCountAttribute()
    {
        return $this->tickets()->where('status', 'pending')->count();
    }

    public function getCapacityLeftAttribute()
    {
        return $this->capacity - $this->tickets()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }
}
