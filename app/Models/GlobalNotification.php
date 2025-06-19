<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GlobalNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'icon',
        'link',
        'is_active',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function dismissedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_notification_dismissals')
            ->withPivot('dismissed_at')
            ->withTimestamps();
    }

    public function isDismissedBy(User $user): bool
    {
        return $this->dismissedByUsers()->where('user_id', $user->id)->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
        });
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeVisibleToUser($query, User $user)
    {
        return $query->active()
            ->published()
            ->notExpired()
            ->whereDoesntHave('dismissedByUsers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
    }

    public function getTypeIconAttribute(): string
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match ($this->type) {
            'announcement' => 'heroicon-o-megaphone',
            'feature' => 'heroicon-o-sparkles',
            'blog' => 'heroicon-o-document-text',
            'podcast' => 'heroicon-o-microphone',
            'video' => 'heroicon-o-play-circle',
            default => 'heroicon-o-bell',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'announcement' => 'blue',
            'feature' => 'green',
            'blog' => 'purple',
            'podcast' => 'orange',
            'video' => 'red',
            default => 'gray',
        };
    }
}