<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    protected $fillable = [
        'asset_id',
        'action',
        'performed_by',
        'old_value',
        'new_value',
    ];

    /**
     * Get the asset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user who performed the action
     */
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
