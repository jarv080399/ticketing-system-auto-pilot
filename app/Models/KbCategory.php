<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbCategory extends Model
{
    /** @use HasFactory<\Database\Factories\KbCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'parent_id',
        'sort_order',
    ];

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(KbCategory::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(KbCategory::class, 'parent_id');
    }

    /**
     * Get the articles for the category.
     */
    public function articles()
    {
        return $this->hasMany(KbArticle::class, 'category_id');
    }
}
