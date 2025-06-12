<?php

namespace App\Console\Commands;

use App\Jobs\FetchTrackingMetricsJob;
use Illuminate\Console\Command;

class FetchTrackingMetrics extends Command
{
    protected $signature = 'tracking:fetch-metrics {--date-ranges=* : Specific date ranges to fetch}';
    protected $description = 'Fetch and cache tracking metrics for all projects';

    public function handle(): int
    {
        $dateRanges = $this->option('date-ranges');

        if (empty($dateRanges)) {
            $dateRanges = null; // Use default ranges
        }

        $this->info('Dispatching tracking metrics fetch job...');

        FetchTrackingMetricsJob::dispatch($dateRanges);

        $this->info('Job dispatched successfully!');

        return self::SUCCESS;
    }
}
