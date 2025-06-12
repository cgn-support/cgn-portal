<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Update extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'content',
        'type',
        'is_public',
        'scheduled_for',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'scheduled_for' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_for');
    }
}
