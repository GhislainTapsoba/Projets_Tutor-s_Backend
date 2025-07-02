<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Models\Ticket;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'district',
        'latitude',
        'longitude',
        'address',
        'phone',
        'email',
        'description',
        'is_active'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class)->orderBy('created_at', 'desc');
    }

    public function notifyAgents($message, $type = 'agency')
    {
        $agents = User::agents()->get();
        foreach ($agents as $agent) {
            $agent->notify(new AgencyNotification($this, $message, $type));
        }
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function calculateDistance($latitude, $longitude)
    {
        $R = 6371; // Rayon de la Terre en km
        $dLat = deg2rad($this->latitude - $latitude);
        $dLon = deg2rad($this->longitude - $longitude);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude)) * cos(deg2rad($this->latitude)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    public function getActiveTicketsCountAttribute()
    {
        return $this->tickets()->where('status', 'pending')->count();
    }

    public function getAverageWaitTimeAttribute()
    {
        return $this->tickets()
            ->whereNotNull('end_time')
            ->whereNotNull('start_time')
            ->avg('wait_time');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
