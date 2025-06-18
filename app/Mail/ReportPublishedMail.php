<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportPublishedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Report $report;
    public string $reportUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
        $this->reportUrl = route('project.report', [
            'uuid' => $report->project_id,
            'report_id' => $report->id
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your {$this->report->report_month_name} {$this->report->report_year} Marketing Report is Ready!",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.report-published',
            with: [
                'report' => $this->report,
                'project' => $this->report->project,
                'reportUrl' => $this->reportUrl,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
