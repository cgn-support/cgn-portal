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
        'report_month',
        'content',
        'metrics_data',
        'file_path',
        'looker_studio_share_link',
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
    
    public function getReportMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->report_month, 1));
    }
    
    public function getReportYearAttribute(): int
    {
        return $this->report_date->year;
    }
}
