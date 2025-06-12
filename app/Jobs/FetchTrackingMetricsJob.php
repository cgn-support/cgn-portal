<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\Tracking\TrackingAnalyticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchTrackingMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutes
    public int $tries = 3;

    private array $dateRanges;

    public function __construct(array $dateRanges = null)
    {
        $this->dateRanges = $dateRanges ?? [
            'today',
            'yesterday',
            'last_7_days',
            'last_30_days',
            'this_month',
        ];
    }

    public function handle(TrackingAnalyticsService $analyticsService): void
    {
        Log::info('Starting tracking metrics fetch job');

        // Get all active projects with tracking URLs
        $projects = Project::whereNotNull('project_url')
            ->where('status', 'active')
            ->get();

        $successCount = 0;
        $errorCount = 0;

        foreach ($projects as $project) {
            try {
                $this->fetchProjectMetrics($project, $analyticsService);
                $successCount++;

                // Small delay between projects to avoid overwhelming the API
                usleep(100000); // 100ms

            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Failed to fetch metrics for project', [
                    'project_id' => $project->id,
                    'domain' => $project->getTrackingDomain(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Tracking metrics fetch job completed', [
            'total_projects' => $projects->count(),
            'successful' => $successCount,
            'failed' => $errorCount,
        ]);
    }

    private function fetchProjectMetrics(Project $project, TrackingAnalyticsService $analyticsService): void
    {
        $domain = $project->getTrackingDomain();

        if (!$domain) {
            Log::warning('Project has no valid tracking domain', [
                'project_id' => $project->id,
                'project_url' => $project->project_url
            ]);
            return;
        }

        foreach ($this->dateRanges as $dateRange) {
            try {
                // This will cache the metrics
                $metrics = $analyticsService->getProjectMetrics($project, $dateRange);

                Log::debug('Cached metrics for project', [
                    'project_id' => $project->id,
                    'domain' => $domain,
                    'date_range' => $dateRange,
                    'visitors' => $metrics['unique_visitors'] ?? 0,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to fetch metrics for date range', [
                    'project_id' => $project->id,
                    'domain' => $domain,
                    'date_range' => $dateRange,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Tracking metrics job failed completely', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
