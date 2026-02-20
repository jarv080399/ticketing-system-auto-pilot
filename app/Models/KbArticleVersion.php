<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbArticleVersion extends Model
{
    /** @use HasFactory<\Database\Factories\KbArticleVersionFactory> */
    use HasFactory;

    protected $fillable = [
        'article_id',
        'version_number',
        'title',
        'content',
        'changed_by',
        'change_summary',
    ];

    /**
     * Get the article this version belongs to.
     */
    public function article()
    {
        return $this->belongsTo(KbArticle::class, 'article_id');
    }

    /**
     * Get the user who changed this version.
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
