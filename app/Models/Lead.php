<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'payload',
        'status',
        'value',
        'is_valid',
        'notes',
        'submitted_at',
        'ip_address',
        'referrer_name',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected $casts = [
        'payload' => 'array',
        'submitted_at' => 'datetime',
        'is_valid' => 'boolean',
        'value' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 'new',
        'is_valid' => false,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Static method to create lead from webhook payload
    public static function createFromWebhook(array $payload): self
    {
        $projectId = $payload['project_id'] ?? null;

        if (!$projectId || !Project::where('id', $projectId)->exists()) {
            throw new \InvalidArgumentException('Valid project_id is required in webhook payload');
        }

        // Parse time_submitted if present
        $submittedAt = null;
        if (isset($payload['time_submitted'])) {
            try {
                // Handle time format like "10.00 AM" - combine with today's date
                $submittedAt = Carbon::createFromFormat('h.i A', $payload['time_submitted']);
            } catch (\Exception $e) {
                $submittedAt = now(); // Fallback to current time
            }
        }

        return self::create([
            'project_id' => $projectId,
            'payload' => $payload,
            'submitted_at' => $submittedAt ?? now(),
            'ip_address' => $payload['ip_address'] ?? request()->ip(),
            'referrer_name' => $payload['referrer_name'] ?? null,
            'utm_source' => $payload['utm_source'] ?? null,
            'utm_medium' => $payload['utm_medium'] ?? null,
            'utm_campaign' => $payload['utm_campaign'] ?? null,
        ]);
    }

    // Helper methods to extract common data from payload
    public function getEmailAttribute(): ?string
    {
        return $this->payload['email'] ??
            $this->payload['Email'] ??
            $this->payload['Field_1'] ?? // Sometimes email might be in Field_1
            null;
    }

    public function getNameAttribute(): ?string
    {
        if (isset($this->payload['name'])) {
            return $this->payload['name'];
        }

        $firstName = $this->payload['first_name'] ?? '';
        $lastName = $this->payload['last_name'] ?? '';

        return trim($firstName . ' ' . $lastName) ?: null;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->payload['phone'] ??
            $this->payload['Phone'] ??
            null;
    }

    public function getCustomFieldAttribute(string $fieldName): ?string
    {
        return $this->payload[$fieldName] ?? null;
    }

    public function getMessageAttribute(): ?string
    {
        return $this->payload['message'] ??
            $this->payload['Message'] ??
            $this->payload['comments'] ??
            $this->payload['inquiry'] ??
            null;
    }

    // Scopes
    public function scopeNewLeads($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    public function scopeInvalid($query)
    {
        return $query->where('is_valid', false);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // Actions
    public function markAsValid(): bool
    {
        $this->is_valid = true;
        $this->status = 'valid';
        return $this->save();
    }

    public function markAsInvalid(): bool
    {
        $this->is_valid = false;
        $this->status = 'invalid';
        return $this->save();
    }

    // Change this method signature (around line 156)
    public function markAsClosed(?float $value = null): bool
    {
        $this->status = 'closed';
        if ($value !== null) {
            $this->value = $value;
        }
        return $this->save();
    }
}
