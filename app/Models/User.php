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
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

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
     * ---------------------------------------------------------------------------------------------------------------
     * RELATIONSHIPS
     * ---------------------------------------------------------------------------------------------------------------
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function uploadedFiles(): MorphMany
    {
        return $this->morphMany(UploadedFile::class, 'uploadable');
    }

    /**
     * ---------------------------------------------------------------------------------------------------------------
     * METHODS
     * ---------------------------------------------------------------------------------------------------------------
     */
    public function profilePicture()
    {
        return $this->uploadedFiles()->where('file_type', 'profile_picture')->latest()->first();
    }

    public function getProfilePictureUrlAttribute(): string
    {
        $profilePicture = $this->profilePicture();

        if ($profilePicture && $profilePicture->exists()) {
            return $profilePicture->url;
        }

        // Return default avatar URL or generate one
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    public function uploadProfilePicture($file): UploadedFile
    {
        $oldProfilePicture = $this->profilePicture();
        if ($oldProfilePicture) {
            $oldProfilePicture->delete();
        }

        $path = $file->store('uploads/users/profiles', 'public');

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
