<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Forum extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'order',
        'is_private',
        'threads_count',
        'posts_count',
        'last_post_id',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_private' => 'boolean',
        'threads_count' => 'integer',
        'posts_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function lastPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'last_post_id');
    }
}
