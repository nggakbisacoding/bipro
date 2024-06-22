<?php

namespace Modules\Keyword\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Log;
use Modules\Post\Entities\Post;

class ExtractSentiment implements ShouldBeEncrypted, ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private int $postId,
        private string $message
    ) {
        $this->onQueue('ExtractSentiment');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sentiment = $this->get_sentiment($this->message);
        Log::info('Post', ['post_id' => $this->postId, 'sentiment' => $sentiment]);
        if (! is_null($sentiment)) {
            $post = Post::firstWhere('id', '=', $this->postId);
            $post->update([
                'stats' => [
                    ...$post->stats ?? [],
                    ...$sentiment,
                ],
            ]);
        }
    }

    private function get_sentiment(string $message)
    {
        $data = [
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You will be provided with a tweet, and your task is to classify its sentiment as positive, neutral, or negative. Additionally, you should analyze the tweet's topic (health / economic / culture / politic / education / social) and assess the level of intolerance, assigning a value from 0 for tolerance to 1 for intolerance. Your response should be in JSON (RFC8259)",
                ],
                [
                    'role' => 'user',
                    'content' => $message,
                ],
            ],
            'model' => 'gpt-3.5-turbo',
            'temperature' => 1,
            'presence_penalty' => 0,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'stream' => false,
        ];

        $headers = [
            'authority' => 'ai.fakeopen.com',
            'accept-language' => 'en-US,en;q=0.9,id;q=0.8',
            'authorization' => 'Bearer pk-ZiQsrhHXqE1N-HftsP6A46oz4C17W8rO5kPY1gRxVOE',
            'content-type' => 'application/json',
            'origin' => 'https://hai.dongstop.link',
            'pragma' => 'no-cache',
            'referer' => 'https://hai.dongstop.link/',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
        ];

        $response = Http::withHeaders($headers)
            ->retry(3, 5000)
            ->post('https://ai.fakeopen.com/v1/chat/completions', $data);

        $response = json_decode($response->body(), true);
        $response = $response['choices'][0]['message']['content'];

        $pattern = '/\\{(?:[^\\{\\}]|(?R))*\\}/';
        preg_match($pattern, $response, $matches);

        if (isset($matches[0])) {
            $json_data = $matches[0];
            $response = $json_data;
        }

        return json_decode($response, true);

    }
}
