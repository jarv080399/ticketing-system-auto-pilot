<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serial_number',
        'asset_tag',
        'type',
        'manufacturer',
        'model',
        'description',
        'status',
        'owner_user_id',
        'purchase_date',
        'warranty_expires_at',
        'purchase_cost',
        'location',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expires_at' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    /**
     * Get the owner of the asset
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * Get the history of the asset.
     */
    public function history()
    {
        return $this->hasMany(AssetHistory::class)->latest();
    }
}
