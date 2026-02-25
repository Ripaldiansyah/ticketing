<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'venue',
        'price',
        'date_start',
        'date_end',
    ];

    protected $casts = [
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the WA account associated with the event.
     */
    public function waAccount()
    {
        return $this->belongsTo(WaAccount::class);
    }
}
