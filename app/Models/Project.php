<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',          // This will now be the CLIENT User ID
        'account_manager_id', // NEW: For the Account Manager User ID
        'business_id',
        'plan_id',
        'name', // Added 'name' as it's usually fillable
        'monday_pulse_id',
        'monday_board_id',
        'portfolio_project_rag',
        'portfolio_project_doc',
        'portfolio_project_scope',
        'project_url',              // Was 'Domain', maps to this
        'current_services',         // NEW
        'completed_services',       // NEW
        'specialist_monday_id',     // NEW
        'content_writer_monday_id', // NEW
        'developer_monday_id',      // NEW
        'copywriter_monday_id',     // NEW
        'designer_monday_id',       // NEW
        'google_drive_folder',      // Was 'Drive Folder ID'
        'client_logo',
        'slack_channel',            // Was 'Slack Channel ID'
        'bright_local_url',         // Was 'Bright Local ID', assuming it's a URL or you store ID as URL
        'google_sheet_id',          // Was 'Project Workbook ID'
        'wp_umbrella_project_id',   // Was 'WP Umbrella ID'
        'project_start_date',
        // 'wordpress_api_url', // Keep if you still need this separate from project_url
        'my_maps_share_link',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'portfolio_project_doc' => 'array',
        'project_start_date' => 'date',
        'current_services' => 'array',   // NEW: Cast comma-separated string to array
        'completed_services' => 'array', // NEW: Cast comma-separated string to array
    ];

    /**
     * The default value for the 'status' attribute.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * Get the client user associated with this project.
     * (user_id now refers to the client)
     */
    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the Account Manager (User) assigned to this project.
     */
    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    /**
     * Get the business this project belongs to.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the service plan associated with this project (optional).
     */
    public function plan(): BelongsTo
    {
        // This assumes you have a 'Plan' model and 'plans' table
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the leads associated with this project.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the client-facing updates for this project.
     */
    public function updates(): HasMany
    {
        return $this->hasMany(Update::class);
    }

    /**
     * Get the internal notes for this project.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Get the monthly reports for this project.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get the tasks associated with this project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
