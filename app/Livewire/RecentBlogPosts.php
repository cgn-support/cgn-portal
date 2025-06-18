<?php

namespace App\Livewire;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class RecentBlogPosts extends Component
{
    public $month;
    public $posts;
    public $project;

    public function mount($month, Project $project)
    {
        $this->month = $month;
        $this->project = $project;
        $this->posts = $this->getPosts();
    }

    public function getPosts()
    {
        $posts = $this->fetchPostsByMonth($this->project->project_url);
        return $this->transformPostsForView($posts);
    }

    public function fetchPostsByMonth($domain, $count = 4)
    {
        $apiUrl = rtrim($domain, '/') . '/wp-json/wp/v2/posts';

        // Parse the month parameter to get start and end dates
        $monthStart = Carbon::parse($this->month)->startOfMonth();
        $monthEnd = Carbon::parse($this->month)->endOfMonth();

        $response = Http::get($apiUrl, [
            'per_page' => 100, // Fetch more posts to filter by date
            '_embed' => true,
            'after' => $monthStart->toISOString(),
            'before' => $monthEnd->toISOString(),
        ]);

        if (!$response->successful()) {
            return [];
        }

        $posts = $response->json();
        
        // Additional client-side filtering to ensure we only get posts from the specified month
        $filteredPosts = collect($posts)->filter(function ($post) use ($monthStart, $monthEnd) {
            $postDate = Carbon::parse(Arr::get($post, 'date'));
            return $postDate->between($monthStart, $monthEnd);
        })->take($count);

        return $filteredPosts->toArray();
    }

    public function transformPostsForView($posts)
    {
        return collect($posts)->map(function ($post) {
            // Try to get the best available thumbnail size
            $thumbnail = null;
            $featuredMedia = Arr::get($post, '_embedded.wp:featuredmedia.0');
            
            if ($featuredMedia) {
                $sizes = Arr::get($featuredMedia, 'media_details.sizes', []);
                // Prefer medium size, fallback to thumbnail, then full
                $thumbnail = Arr::get($sizes, 'medium.source_url') 
                    ?? Arr::get($sizes, 'thumbnail.source_url')
                    ?? Arr::get($featuredMedia, 'source_url');
            }
            
            return [
                'title' => Arr::get($post, 'title.rendered'),
                'date' => Carbon::parse(Arr::get($post, 'date'))->format('M d, Y'),
                'link' => Arr::get($post, 'link'),
                'thumbnail' => $thumbnail,
                'excerpt' => strip_tags(Arr::get($post, 'excerpt.rendered', '')),
            ];
        });
    }

    public function render()
    {
        return view('livewire.recent-blog-posts');
    }
}
