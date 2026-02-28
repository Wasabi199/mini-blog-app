<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class UploadedFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'disk',
    ];

    /**
     * Get the parent uploadable model (User or Post).
     */
    public function uploadable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the full URL of the uploaded file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    /**
     * Get the full path of the uploaded file.
     */
    public function getFullPathAttribute(): string
    {
        return Storage::disk($this->disk)->path($this->file_path);
    }

    /**
     * Check if the file exists in storage.
     */
    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->file_path);
    }

    /**
     * Delete the file from storage.
     */
    public function deleteFile(): bool
    {
        if ($this->exists()) {
            return Storage::disk($this->disk)->delete($this->file_path);
        }
        
        return false;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically delete the file from storage when the model is deleted
        static::deleting(function ($uploadedFile) {
            $uploadedFile->deleteFile();
        });
    }
}
