<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KbArticle extends Model
{
    /** @use HasFactory<\Database\Factories\KbArticleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'visibility',
        'status',
        'author_id',
        'category_id',
        'view_count',
        'helpful_yes',
        'helpful_no',
    ];

    /**
     * Get the author of the article.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the category of the article.
     */
    public function category()
    {
        return $this->belongsTo(KbCategory::class, 'category_id');
    }

    /**
     * Get the tags associated with the article.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'kb_article_tags', 'article_id', 'tag_id');
    }

    /**
     * Get the versions for the article.
     */
    public function versions()
    {
        return $this->hasMany(KbArticleVersion::class, 'article_id')->orderBy('version_number', 'desc');
    }
}
