<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
        'response_time_minutes',
        'resolution_time_minutes',
        'business_hours_only',
    ];

    protected $casts = [
        'response_time_minutes' => 'integer',
        'resolution_time_minutes' => 'integer',
        'business_hours_only' => 'boolean',
    ];
}
