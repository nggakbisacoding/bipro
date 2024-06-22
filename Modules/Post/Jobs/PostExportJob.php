<?php

namespace Modules\Post\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Keyword\Entities\Keyword;

class PostExportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $posts = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $posts)
    {
        $this->posts = $posts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $targetDirectory = storage_path('framework/cache/exports');

        if (! is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        $targetFilename = $targetDirectory.DIRECTORY_SEPARATOR.$this->batchId.'.csv';
        $targetFile = fopen($targetFilename, 'a');

        // Add header if file not exists
        if (! file_exists($targetFilename)) {
            fputcsv($targetFile, [
                'Username',
                'Name',
                'Message',
                'Date',
                'Location',
                'Sentiment',
                'Intolerance',
                'Topic',
                'Link',
                'Likes',
                'Replies',
                'Retweets',
                'Quote Retweets',
            ]);
        }

        $content = '';
        foreach ($this->posts as $post) {
            $post['like_count'] = $post['stats']['like'] ?? 0;
            $post['reply_count'] = $post['stats']['reply'] ?? 0;
            $post['retweet_count'] = $post['stats']['retweet'] ?? 0;

            $stats = $post['stats'];
            $link = '';

            if ($post['postable']['source'] === Keyword::SOURCE_TWITTER) {
                $postId = $post['post_id'];
                $username = $post['username'];
                $link = "https://twitter.com/{$username}/status/{$postId}";
            }

            $sentiment = $stats['sentiment'] ?? '';
            if (is_array($sentiment)) {
                $sentiment = isset($sentiment['sentiment']) ? $sentiment['sentiment'] : '';
            }

            $data = [
                'username' => $post['username'],
                'name' => $post['name'],
                'message' => $post['message'],
                'date' => $post['date'],
                'location' => $stats['location'] ?? '',
                'sentiment' => $sentiment,
                'intolerance' => $post['stats']['intolerance'] ?? '',
                'topic' => $post['stats']['topic'] ?? '',
                'link' => $link,
                'likes' => $stats['like'] ?? 0,
                'replies' => $stats['reply'] ?? 0,
                'retweets' => $stats['retweet'] ?? 0,
                'quote_retweets' => $stats['quote_retweet'] ?? 0,
            ];

            $post = implode('","', $data);
            $post = '"'.$post.'"';
            $content .= $post."\n";
        }

        fwrite($targetFile, $content);
        fclose($targetFile);
    }
}
