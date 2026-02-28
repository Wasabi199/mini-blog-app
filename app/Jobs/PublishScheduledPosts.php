<?php

namespace App\Jobs;

use App\Models\Post;
use App\PostStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PublishScheduledPosts implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find all draft posts with a published_at date that is now or in the past
        $posts = Post::where('status', PostStatus::DRAFT)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        foreach ($posts as $post) {
            $post->update([
                'status' => PostStatus::PUBLISHED,
            ]);

            Log::info("Post '{$post->title}' (ID: {$post->id}) has been automatically published.");
        }

        if ($posts->count() > 0) {
            Log::info("Published {$posts->count()} scheduled post(s).");
        }
    }
}
