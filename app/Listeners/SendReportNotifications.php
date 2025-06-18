<?php

namespace App\Listeners;

use App\Events\ReportPublished;
use App\Mail\ReportPublishedMail;
use App\Services\SlackNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReportNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    private SlackNotificationService $slackService;

    /**
     * Create the event listener.
     */
    public function __construct(SlackNotificationService $slackService)
    {
        $this->slackService = $slackService;
    }

    /**
     * Handle the event.
     */
    public function handle(ReportPublished $event): void
    {
        $report = $event->report;
        $project = $report->project;

        Log::info('Processing report published notifications', [
            'report_id' => $report->id,
            'project_id' => $project->id
        ]);

        // Send email notification to client
        $this->sendEmailNotification($report);

        // Send Slack notification
        $this->sendSlackNotification($report);
    }

    /**
     * Send email notification to client
     */
    private function sendEmailNotification(Report $report): void
    {
        try {
            $clientUser = $report->project->clientUser;
            
            if (!$clientUser || !$clientUser->email) {
                Log::warning('No client email found for report notification', [
                    'report_id' => $report->id,
                    'project_id' => $report->project_id
                ]);
                return;
            }

            Mail::to($clientUser->email)
                ->send(new ReportPublishedMail($report));

            Log::info('Email notification sent successfully', [
                'report_id' => $report->id,
                'email' => $clientUser->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'report_id' => $report->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send Slack notification
     */
    private function sendSlackNotification(Report $report): void
    {
        try {
            $success = $this->slackService->sendReportPublishedNotification($report);
            
            if ($success) {
                Log::info('Slack notification sent successfully', [
                    'report_id' => $report->id
                ]);
            } else {
                Log::warning('Slack notification failed', [
                    'report_id' => $report->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception sending Slack notification', [
                'report_id' => $report->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ReportPublished $event, \Throwable $exception): void
    {
        Log::error('Report notification job failed', [
            'report_id' => $event->report->id,
            'error' => $exception->getMessage()
        ]);
    }
}
