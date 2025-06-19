<?php

namespace Database\Seeders;

use App\Models\PresetTask;
use Illuminate\Database\Seeder;

class PresetTaskSeeder extends Seeder
{
    public function run(): void
    {
        $presetTasks = [
            [
                'title' => 'Provide Business Information',
                'description' => 'Please provide your business hours, contact information, services offered, and any other relevant business details that should be included in your marketing materials.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Submit Brand Assets',
                'description' => 'Upload your logo files, brand colors, fonts, and any existing marketing materials. This helps us maintain brand consistency across all marketing efforts.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Review Website Content',
                'description' => 'Please review the draft website content and provide feedback. Check for accuracy, tone, and any missing information about your services.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Approve Google Business Profile',
                'description' => 'Review and approve your Google Business Profile setup including business description, categories, photos, and hours.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Provide Customer Testimonials',
                'description' => 'Submit 3-5 customer testimonials with permission to use them in marketing materials. Include customer name and any relevant details.',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Complete SEO Questionnaire',
                'description' => 'Fill out the detailed SEO questionnaire to help us understand your target keywords, competitors, and local market focus.',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Review Social Media Strategy',
                'description' => 'Review the proposed social media content strategy and posting schedule. Provide feedback on tone, frequency, and content types.',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'title' => 'Approve Marketing Materials',
                'description' => 'Review and approve the created marketing materials including flyers, business cards, and promotional content before printing/distribution.',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'title' => 'Setup Analytics Access',
                'description' => 'Provide access to your existing Google Analytics, Google Ads, and social media accounts so we can track performance and optimize campaigns.',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'title' => 'Final Project Review',
                'description' => 'Conduct a final review of all completed work including website, SEO setup, and marketing materials. Sign off on project completion.',
                'sort_order' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($presetTasks as $task) {
            PresetTask::create($task);
        }
    }
}