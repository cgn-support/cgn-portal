<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'account_manager_id',
        'title',
        'report_date',
        'content',
        'metrics_data',
        'file_path',
        'status',
        'notes',
    ];

    protected $casts = [
        'report_date' => 'date',
        'metrics_data' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }
}
