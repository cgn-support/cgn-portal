<?php

namespace App\Services;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class KeywordApiService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.keywords_com.api_key');
        $this->baseUrl = config('services.keywords_com.url');
    }

    /**
     * Get keyword rankings for a project on a specific date
     */
    public function getProjectKeywords(Project $project, string $date = null): array
    {
        if (!$project->keywords_com_project_name) {
            Log::warning("Project {$project->id} does not have a keywords.com project name configured");
            return $this->getEmptyKeywordData();
        }

        if (!$this->apiKey) {
            Log::error('Keywords.com API key not configured');
            return $this->getEmptyKeywordData();
        }

        $date = $date ?? now()->format('Y-m-d');
        $cacheKey = "keywords_rankings_{$project->id}_{$date}";
        $cacheTtl = 7 * 24 * 60 * 60; // 7 days in seconds

        return Cache::remember($cacheKey, $cacheTtl, function () use ($project, $date) {
            return $this->fetchKeywordData($project, $date);
        });
    }

    /**
     * Fetch keyword data from the API
     */
    private function fetchKeywordData(Project $project, string $date): array
    {
        try {
            $url = $this->buildApiUrl($project->keywords_com_project_name, $date);
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::error("Keywords.com API error for project {$project->id}: " . $response->body());
                return $this->getEmptyKeywordData();
            }

            $data = $response->json();
            return $this->processKeywordData($data);

        } catch (\Exception $e) {
            Log::error("Exception fetching keywords data for project {$project->id}: " . $e->getMessage());
            return $this->getEmptyKeywordData();
        }
    }

    /**
     * Build the API URL for fetching keywords
     */
    private function buildApiUrl(string $projectName, string $date): string
    {
        $encodedProjectName = urlencode($projectName);
        return "{$this->baseUrl}/groups/{$encodedProjectName}/keywords/?per_page=250&page=1&date={$date}";
    }

    /**
     * Process the keyword data and calculate ranking metrics
     */
    private function processKeywordData(array $data): array
    {
        $keywords = $data['data'] ?? [];
        
        $keywordsInTop3 = 0;
        $keywordsInTop10 = 0;
        $totalKeywords = count($keywords);

        foreach ($keywords as $keyword) {
            $rank = $keyword['attributes']['grank'] ?? 0;
            
            // Only count keywords that have a ranking (greater than 0)
            if ($rank > 0) {
                if ($rank <= 3) {
                    $keywordsInTop3++;
                }
                
                if ($rank <= 10) {
                    $keywordsInTop10++;
                }
            }
        }

        return [
            'keywords_in_top_3' => $keywordsInTop3,
            'keywords_in_top_10' => $keywordsInTop10,
            'total_keywords' => $totalKeywords,
            'keywords_with_rankings' => $this->countKeywordsWithRankings($keywords),
        ];
    }

    /**
     * Count keywords that have actual rankings (not 0)
     */
    private function countKeywordsWithRankings(array $keywords): int
    {
        return collect($keywords)->filter(function ($keyword) {
            return ($keyword['attributes']['grank'] ?? 0) > 0;
        })->count();
    }

    /**
     * Get empty keyword data structure
     */
    private function getEmptyKeywordData(): array
    {
        return [
            'keywords_in_top_3' => 0,
            'keywords_in_top_10' => 0,
            'total_keywords' => 0,
            'keywords_with_rankings' => 0,
        ];
    }

    /**
     * Get keyword data for multiple dates for trend analysis
     */
    public function getKeywordTrends(Project $project, array $dates): array
    {
        $trends = [];
        
        foreach ($dates as $date) {
            $trends[$date] = $this->getProjectKeywords($project, $date);
        }
        
        return $trends;
    }

    /**
     * Get comparison data for a previous period
     */
    public function getComparisonKeywordData(Project $project, string $currentDate, string $previousDate): array
    {
        $current = $this->getProjectKeywords($project, $currentDate);
        $previous = $this->getProjectKeywords($project, $previousDate);

        return [
            'current' => $current,
            'previous' => $previous,
            'trends' => [
                'top_3_change' => $current['keywords_in_top_3'] - $previous['keywords_in_top_3'],
                'top_10_change' => $current['keywords_in_top_10'] - $previous['keywords_in_top_10'],
            ]
        ];
    }
}