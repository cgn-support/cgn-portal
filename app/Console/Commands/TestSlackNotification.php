<?php

namespace App\Console\Commands;

use App\Models\Report;
use App\Services\SlackNotificationService;
use Illuminate\Console\Command;

class TestSlackNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:slack {--report-id= : Test with specific report ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Slack notification functionality';

    /**
     * Execute the console command.
     */
    public function handle(SlackNotificationService $slackService)
    {
        $this->info('Testing Slack notifications...');

        // Test basic connection first
        $this->info('Testing Slack connection...');
        if ($slackService->testSlackConnection()) {
            $this->info('✅ Slack connection test successful!');
        } else {
            $this->error('❌ Slack connection test failed. Check your SLACK_WEBHOOK_URL in .env');
            return 1;
        }

        // Test with a specific report if provided
        $reportId = $this->option('report-id');
        if ($reportId) {
            $report = Report::find($reportId);
            if (!$report) {
                $this->error("Report with ID {$reportId} not found.");
                return 1;
            }

            $this->info("Testing report notification for Report ID: {$reportId}");
            if ($slackService->sendReportPublishedNotification($report)) {
                $this->info('✅ Report notification sent successfully!');
            } else {
                $this->error('❌ Failed to send report notification.');
                return 1;
            }
        } else {
            // Try to find any available report
            $report = Report::with('project')->first();
            if ($report) {
                $this->info("Testing with Report ID: {$report->id}");
                if ($slackService->sendReportPublishedNotification($report)) {
                    $this->info('✅ Test report notification sent successfully!');
                } else {
                    $this->error('❌ Failed to send test report notification.');
                    return 1;
                }
            } else {
                $this->warn('No reports found in database. Create a report first to test report notifications.');
            }
        }

        $this->info('🎉 Slack notification test completed!');
        return 0;
    }
}
