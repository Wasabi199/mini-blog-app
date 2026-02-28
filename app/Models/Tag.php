<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * ---------------------------------------------------------------------------------------------------------------
     * BOOT
     * ---------------------------------------------------------------------------------------------------------------
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * ---------------------------------------------------------------------------------------------------------------
     * RELATIOSHIPS
     * ---------------------------------------------------------------------------------------------------------------
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }

    /**
     * ---------------------------------------------------------------------------------------------------------------
     * SCOPES
     * ---------------------------------------------------------------------------------------------------------------
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit);
    }

    public function scopeWithPostCount($query)
    {
        return $query->withCount('posts');
    }
}
