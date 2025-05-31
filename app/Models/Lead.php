<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon; // For type hinting if needed

/**
 * App\Models\Lead
 *
 * @property int $id
 * @property int $project_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $ip_address
 * @property Carbon|null $submitted_at
 * @property string|null $session_id
 * @property string|null $referrer_name
 * @property string|null $initial_referrer
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property array|null $payload_data
 * @property string $status
 * @property float|null $value
 * @property bool $is_valid
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Project $project
 *
 * @method static \Database\Factories\LeadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead query()
 *
 * @mixin \Eloquent
 */
class Lead extends Model
{
    use HasFactory; // If you plan to use model factories

    /**
     * The attributes that are mass assignable.
     *
     * These attributes can be filled using `Lead::create()` or `Lead::update()`
     * with an array of data.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'ip_address',
        'submitted_at',
        'session_id',
        'referrer_name',
        'initial_referrer',
        'utm_source',
        'utm_medium',
        'payload_data',
        'status',
        'value',
        'is_valid',
        'notes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * This ensures that when you access these attributes, they are automatically
     * converted to the specified type.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submitted_at' => 'datetime', // Converts 'submitted_at' to a Carbon instance
        'payload_data' => 'array',    // Decodes/encodes the 'payload_data' JSON column to/from an array
        'is_valid' => 'boolean',      // Casts 'is_valid' to true/false
        'value' => 'decimal:2',     // Casts 'value' to a float with 2 decimal places (use string for precise financial math)
        // Note: For precise financial calculations, consider using a library like brick/money
        // and potentially casting to a custom Money object or keeping as string and handling in app.
        // For display and simple storage, 'decimal:2' is often fine.
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // You can add other model methods here as needed, for example:

    /**
     * Get the full name of the lead.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Scope a query to only include new leads.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNewLeads($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Mark the lead as valid.
     */
    public function markAsValid(): bool
    {
        $this->is_valid = true;

        return $this->save();
    }

    /**
     * Mark the lead as invalid.
     */
    public function markAsInvalid(): bool
    {
        $this->is_valid = false;

        return $this->save();
    }

    /**
     * Update the lead status.
     */
    public function updateStatus(string $newStatus): bool
    {
        // Optional: Add validation here to ensure $newStatus is one of the allowed enum values
        // if (!in_array($newStatus, ['new', 'lost', 'estimate', 'closed'])) {
        //     throw new \InvalidArgumentException("Invalid lead status: {$newStatus}");
        // }
        $this->status = $newStatus;

        return $this->save();
    }
}
