<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'session_id',
        'number',
        'is_active',
    ];

    /**
     * Get the events associated with this WA account.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
