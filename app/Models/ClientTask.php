<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'preset_task_id',
        'assigned_by',
        'title',
        'description',
        'link',
        'is_completed',
        'completed_at',
        'due_date',
        'completion_notes',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function presetTask(): BelongsTo
    {
        return $this->belongsTo(PresetTask::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_completed', false)
                    ->where('due_date', '<', now());
    }

    public function markAsCompleted(?string $notes = null): bool
    {
        $this->is_completed = true;
        $this->completed_at = now();
        if ($notes) {
            $this->completion_notes = $notes;
        }
        return $this->save();
    }

    public function getIsOverdueAttribute(): bool
    {
        return !$this->is_completed && 
               $this->due_date && 
               $this->due_date->isPast();
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_completed) {
            return 'completed';
        }
        
        if ($this->is_overdue) {
            return 'overdue';
        }
        
        return 'pending';
    }
}