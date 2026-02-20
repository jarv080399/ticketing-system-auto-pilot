<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscalationTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'assigned_team',
        'sla_minutes',
        'notification_channels',
    ];

    protected $casts = [
        'level' => 'integer',
        'sla_minutes' => 'integer',
        'notification_channels' => 'array',
    ];
}
