<?php

namespace App\Models;

use App\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function scopePublished($query)
    {
        return $query->where('status', PostStatus::PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', PostStatus::DRAFT);
    }

    public function isPublished(): bool
    {
        return $this->status === PostStatus::PUBLISHED
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    public function isDraft(): bool
    {
        return $this->status === PostStatus::DRAFT;
    }

    /**
     * Get all of the post's uploaded files.
     */
    public function uploadedFiles(): MorphMany
    {
        return $this->morphMany(UploadedFile::class, 'uploadable');
    }

    /**
     * Get the post's thumbnail.
     */
    public function thumbnail()
    {
        return $this->uploadedFiles()->where('file_type', 'thumbnail')->latest()->first();
    }

    /**
     * Get the thumbnail URL or return a default.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $thumbnail = $this->thumbnail();

        if ($thumbnail && $thumbnail->exists()) {
            return $thumbnail->url;
        }

        // Return null or a default placeholder image
        return null;
    }

    /**
     * Upload a new thumbnail.
     */
    public function uploadThumbnail($file): UploadedFile
    {
        // Delete old thumbnail if exists
        $oldThumbnail = $this->thumbnail();
        if ($oldThumbnail) {
            $oldThumbnail->delete();
        }

        // Store the new file
        $path = $file->store('uploads/posts/thumbnails', 'public');

        // Create the uploaded file record
        return $this->uploadedFiles()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => 'thumbnail',
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'disk' => 'public',
        ]);
    }
}
