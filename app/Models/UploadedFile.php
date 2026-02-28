<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class UploadedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'disk',
    ];

    /**
     * ---------------------------------------------------------------------------------------------------------------
     * BOOT
     * ---------------------------------------------------------------------------------------------------------------
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($uploadedFile) {
            $uploadedFile->deleteFile();
        });
    }

    /**
     * ---------------------------------------------------------------------------------------------------------------
     * RELATIOSHIPS
     * ---------------------------------------------------------------------------------------------------------------
     */
    public function uploadable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * ---------------------------------------------------------------------------------------------------------------
     * METHODS
     * ---------------------------------------------------------------------------------------------------------------
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    public function getFullPathAttribute(): string
    {
        return Storage::disk($this->disk)->path($this->file_path);
    }

    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->file_path);
    }

    public function deleteFile(): bool
    {
        if ($this->exists()) {
            return Storage::disk($this->disk)->delete($this->file_path);
        }

        return false;
    }
}
