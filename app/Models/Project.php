<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// For Laravel 9+ UUID handling

// Optional: if you want soft deletes for projects

class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    // Added HasUuids and SoftDeletes (optional)

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    // public $incrementing = false; // Not needed if using HasUuids trait

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    // protected $keyType = 'string'; // Not needed if using HasUuids trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Account Manager responsible for the project
        'business_id', // The business this project belongs to (IMPORTANT: ensure this is in your migration)
        'plan_id', // Optional: if projects are tied to specific service plans
        'monday_pulse_id',
        'monday_board_id',
        'portfolio_project_rag', // RAG status (Red, Amber, Green)
        'portfolio_project_doc', // JSON for project documentation/details
        'portfolio_project_scope',
        'google_sheet_id', // For content topics or other data
        'bright_local_url', // Link to Bright Local reporting dashboard
        'project_start_date',
        'project_url', // Client's project website URL (e.g., the live website)
        'wordpress_api_url', // Specific URL for WordPress API if different from project_url and needed for content pulling
        'google_drive_folder',
        'my_maps_share_link',
        'wp_umbrella_project_id',
        'status', // e.g., 'active', 'paused', 'completed', 'cancelled'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'portfolio_project_doc' => 'array', // Casts the JSON column to an array
        'project_start_date' => 'date',     // Casts to a Carbon date instance (not datetime)
        // 'id' => 'string', // Not strictly necessary with HasUuids but good for clarity
    ];

    /**
     * The default value for the 'status' attribute.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'active', // Default project status
    ];

    // If not using HasUuids (e.g., older Laravel versions or custom UUID generation):
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         if (empty($model->{$model->getKeyName()})) {
    //             $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
    //         }
    //     });
    // }

    /**
     * Get the user (Account Manager) assigned to this project.
     */
    public function accountManager(): BelongsTo
    {
        // Renamed from user() to be more descriptive, assuming user_id is for the AM
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        // Alias for backward compatibility or other uses
        return $this->accountManager();
    }

    /**
     * Get the business this project belongs to.
     * This assumes you have added 'business_id' to your 'projects' table.
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
        // This assumes your 'Lead' model has a 'project_id' foreign key
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the client-facing updates for this project.
     */
    public function updates(): HasMany
    {
        // This assumes your 'Update' model has a 'project_id' foreign key
        return $this->hasMany(Update::class);
    }

    /**
     * Get the internal notes for this project.
     */
    public function notes(): HasMany
    {
        // This assumes your 'Note' model has a 'project_id' foreign key
        return $this->hasMany(Note::class);
    }

    /**
     * Get the monthly reports for this project.
     */
    public function reports(): HasMany
    {
        // This assumes your 'Report' model has a 'project_id' foreign key
        return $this->hasMany(Report::class);
    }

    /**
     * Get the tasks associated with this project.
     */
    public function tasks(): HasMany
    {
        // This assumes your 'Task' model has a 'project_id' foreign key
        return $this->hasMany(Task::class);
    }
}
