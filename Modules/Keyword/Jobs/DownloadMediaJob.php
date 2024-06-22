<?php

namespace Modules\Keyword\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadMediaJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private string $url,
        private string $path
    ) {
        $this->onQueue('DownloadMedia');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $isExists = Storage::exists($this->path);

        if ($isExists) {
            return;
        }

        $response = Http::retry(3, 5000)
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.142.86 Safari/537.36')
            ->get($this->url);

        if ($response->ok()) {

            Storage::put($this->path, $response->body());
        }
    }
}
