<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationService
{
    /**
     * Send report published notification to Slack
     */
    public function sendReportPublishedNotification(Report $report): bool
    {
        try {
            $project = $report->project;
            
            if (!$project || !$project->slack_channel) {
                Log::info('No Slack channel configured for project', [
                    'project_id' => $project?->id,
                    'report_id' => $report->id
                ]);
                return false;
            }

            $slackWebhookUrl = $this->getSlackWebhookUrl($project);
            
            if (!$slackWebhookUrl) {
                Log::warning('No Slack webhook URL configured');
                return false;
            }

            $message = $this->buildReportMessage($report);
            
            $response = Http::post($slackWebhookUrl, $message);

            if ($response->successful()) {
                Log::info('Slack notification sent successfully', [
                    'report_id' => $report->id,
                    'project_id' => $project->id
                ]);
                return true;
            } else {
                Log::error('Failed to send Slack notification', [
                    'report_id' => $report->id,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Exception sending Slack notification', [
                'report_id' => $report->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Build Slack message payload for report
     */
    private function buildReportMessage(Report $report): array
    {
        $project = $report->project;
        $reportUrl = route('project.report', [
            'uuid' => $project->id,
            'report_id' => $report->id
        ]);

        $blocks = [
            [
                'type' => 'header',
                'text' => [
                    'type' => 'plain_text',
                    'text' => '📊 New Marketing Report Published'
                ]
            ],
            [
                'type' => 'section',
                'fields' => [
                    [
                        'type' => 'mrkdwn',
                        'text' => "*Report Period:*\n{$report->report_month_name} {$report->report_year}"
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => "*Project:*\n{$project->display_name}"
                    ]
                ]
            ]
        ];

        // Add description if available
        if ($report->content) {
            $cleanContent = strip_tags($report->content);
            $truncatedContent = strlen($cleanContent) > 150 
                ? substr($cleanContent, 0, 150) . '...' 
                : $cleanContent;

            $blocks[] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*Highlights:*\n{$truncatedContent}"
                ]
            ];
        }

        // Add action buttons
        $buttons = [
            [
                'type' => 'button',
                'text' => [
                    'type' => 'plain_text',
                    'text' => 'View Report 📈'
                ],
                'url' => $reportUrl,
                'style' => 'primary'
            ]
        ];

        // Add Looker Studio link if available
        if ($report->looker_studio_share_link) {
            $buttons[] = [
                'type' => 'button',
                'text' => [
                    'type' => 'plain_text',
                    'text' => 'Full Analytics 📊'
                ],
                'url' => $report->looker_studio_share_link
            ];
        }

        $blocks[] = [
            'type' => 'actions',
            'elements' => $buttons
        ];

        return [
            'channel' => $project->slack_channel,
            'blocks' => $blocks,
            'unfurl_links' => false,
            'unfurl_media' => false
        ];
    }

    /**
     * Get Slack webhook URL from configuration
     */
    private function getSlackWebhookUrl(Project $project): ?string
    {
        // You can configure this per project or use a global webhook
        // For now, we'll use a global webhook URL from environment
        return config('services.slack.webhook_url');
    }

    /**
     * Test Slack connection
     */
    public function testSlackConnection(): bool
    {
        try {
            $webhookUrl = config('services.slack.webhook_url');
            
            if (!$webhookUrl) {
                return false;
            }

            $testMessage = [
                'text' => '🧪 Test message from ' . config('app.name'),
                'blocks' => [
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => '✅ Slack integration is working correctly!'
                        ]
                    ]
                ]
            ];

            $response = Http::post($webhookUrl, $testMessage);
            
            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Slack connection test failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send notification to specific channel
     */
    public function sendToChannel(string $channel, array $message): bool
    {
        try {
            $webhookUrl = config('services.slack.webhook_url');
            
            if (!$webhookUrl) {
                return false;
            }

            $message['channel'] = $channel;
            
            $response = Http::post($webhookUrl, $message);
            
            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Failed to send Slack message to channel', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}