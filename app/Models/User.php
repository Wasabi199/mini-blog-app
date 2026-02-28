<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's posts
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get all of the user's uploaded files.
     */
    public function uploadedFiles(): MorphMany
    {
        return $this->morphMany(UploadedFile::class, 'uploadable');
    }

    /**
     * Get the user's profile picture.
     */
    public function profilePicture()
    {
        return $this->uploadedFiles()->where('file_type', 'profile_picture')->latest()->first();
    }

    /**
     * Get the profile picture URL or return a default.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        $profilePicture = $this->profilePicture();
        
        if ($profilePicture && $profilePicture->exists()) {
            return $profilePicture->url;
        }
        
        // Return default avatar URL or generate one
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Upload a new profile picture.
     */
    public function uploadProfilePicture($file): UploadedFile
    {
        // Delete old profile picture if exists
        $oldProfilePicture = $this->profilePicture();
        if ($oldProfilePicture) {
            $oldProfilePicture->delete();
        }

        // Store the new file
        $path = $file->store('uploads/users/profiles', 'public');

        // Create the uploaded file record
        return $this->uploadedFiles()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => 'profile_picture',
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'disk' => 'public',
        ]);
    }
}
