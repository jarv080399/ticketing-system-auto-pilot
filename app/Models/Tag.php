<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the articles associated with the tag.
     */
    public function articles()
    {
        return $this->belongsToMany(KbArticle::class, 'kb_article_tags', 'tag_id', 'article_id');
    }
}
