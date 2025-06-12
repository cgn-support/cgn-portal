<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'account_manager_id',
        'business_id',
        'plan_id',
        'name',
        'monday_pulse_id',
        'monday_board_id',
        'portfolio_project_rag',
        'portfolio_project_doc',
        'portfolio_project_scope',
        'project_url',
        'current_services',
        'completed_services',
        'specialist_monday_id',
        'content_writer_monday_id',
        'developer_monday_id',
        'copywriter_monday_id',
        'designer_monday_id',
        'google_drive_folder',
        'client_logo',
        'slack_channel',
        'bright_local_url',
        'google_sheet_id',
        'wp_umbrella_project_id',
        'project_start_date',
        'my_maps_share_link',
        'status',
    ];

    protected $casts = [
        'portfolio_project_doc' => 'array',
        'project_start_date' => 'date',
        'current_services' => 'array',
        'completed_services' => 'array',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * Get the dynamically generated display name for the project.
     *
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        // If the project has its own 'name' field filled, prioritize that.
        if (!empty($this->attributes['name'])) {
            return $this->attributes['name'];
        }

        // Fallback to Business and Plan names if the project's own name is not set
        $businessName = $this->business ? $this->business->name : 'No Business';
        $planName = $this->plan ? $this->plan->name : 'No Plan';

        return $businessName . ' | ' . $planName;
    }

    // Relationships
    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function plan(): BelongsTo
    {
        // This assumes you have a 'Plan' model and 'plans' table
        return $this->belongsTo(Plan::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(Update::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Add this method to your Project model
    public function getTrackingDomain(): ?string
    {
        if (!$this->project_url) {
            return null;
        }

        try {
            $parsed = parse_url($this->project_url);
            $domain = $parsed['host'] ?? null;

            // Remove www. prefix if present
            if ($domain && str_starts_with($domain, 'www.')) {
                $domain = substr($domain, 4);
            }

            return $domain;
        } catch (\Exception $e) {
            Log::warning('Failed to parse project URL', [
                'project_id' => $this->id,
                'project_url' => $this->project_url,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
}
