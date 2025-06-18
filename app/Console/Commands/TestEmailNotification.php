<?php

namespace App\Console\Commands;

use App\Mail\ReportPublishedMail;
use App\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email} {--report-id= : Test with specific report ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notification functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $reportId = $this->option('report-id');

        $this->info("Testing email notifications to: {$email}");

        // Find report to test with
        if ($reportId) {
            $report = Report::find($reportId);
            if (!$report) {
                $this->error("Report with ID {$reportId} not found.");
                return 1;
            }
        } else {
            $report = Report::with('project')->first();
            if (!$report) {
                $this->error('No reports found in database. Create a report first to test email notifications.');
                return 1;
            }
        }

        $this->info("Testing with Report ID: {$report->id} - {$report->report_month_name} {$report->report_year}");

        try {
            Mail::to($email)->send(new ReportPublishedMail($report));
            $this->info('✅ Test email sent successfully!');
            
            $this->info('📧 Email details:');
            $this->line("  Subject: Your {$report->report_month_name} {$report->report_year} Marketing Report is Ready!");
            $this->line("  To: {$email}");
            $this->line("  Report: {$report->report_month_name} {$report->report_year}");
            $this->line("  Project: {$report->project->display_name}");

        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email.');
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        $this->info('🎉 Email notification test completed!');
        return 0;
    }
}
