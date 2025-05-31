<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// If using soft deletes

class Client extends Model
{
    use HasFactory, SoftDeletes;

    // Add SoftDeletes if you included it in migration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'primary_contact_name',
        'primary_contact_email',
        'primary_contact_phone',
        'primary_contact_title',
        'preferred_comms_method',
        'hubspot_company_record',
        'signing_date',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'status' => ClientStatus::class, // If using a PHP 8.1 Enum for status
    ];

    /**
     * Get the users (client portal users) associated with this client company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the businesses (operational entities/brands) owned or managed by this client.
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    /**
     * Get all projects associated with this client through its businesses.
     * This is an example of a hasManyThrough relationship if projects primarily belong to businesses.
     */
    public function projects(): HasManyThrough
    {
        // A Client has many Projects through its Businesses
        // Assumes Business model has a 'projects()' relationship defined
        return $this->hasManyThrough(Project::class, Business::class);
    }


    /**
     * Get the support tickets associated with this client.
     * (Assuming Ticket model has a client_id)
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}

