<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger_event',
        'condition_json',
        'action_json',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'condition_json' => 'array',
        'action_json' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];
}
