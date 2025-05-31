<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// If using soft deletes

class Business extends Model
{
    use HasFactory, SoftDeletes;

    // Add SoftDeletes if you included it in migration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'country',
        'phone_number',
        'website_url',
        'google_maps_url',
        'gmb_listing_id',
        'industry',
        'slack_channel_id',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'status' => BusinessStatus::class, // If using a PHP 8.1 Enum for status
    ];

    /**
     * Get the client company that owns or manages this business.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the projects associated with this business.
     * (Assuming Project model has a business_id)
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the leads generated for this business.
     * (Assuming Lead model has a business_id - you might need to add this to your Lead model/migration)
     */
    public function leads(): HasMany
    {
        // IMPORTANT: You'll need to decide if leads belong to a Project or directly to a Business.
        // If leads belong to a Project, this relationship might be different or through projects.
        // If leads can also belong directly to a Business (e.g., general inquiries not tied to a project yet),
        // then your Lead model would need a `business_id` foreign key.
        // For now, assuming leads can be directly tied to a business:
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the reports generated for this business.
     * (Assuming Report model has a business_id)
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
