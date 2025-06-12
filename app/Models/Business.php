<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

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
        'gbp_listing_id',
        'industry',
        'slack_channel_id',
        'status',
        'notes',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function leads(): HasManyThrough
    {
        return $this->hasManyThrough(Lead::class, Project::class);
    }

    public function reports(): HasManyThrough
    {
        return $this->hasManyThrough(Report::class, Project::class);
    }

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class, Project::class);
    }
}
