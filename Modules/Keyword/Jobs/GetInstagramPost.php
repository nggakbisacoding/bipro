<?php

namespace Modules\Keyword\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use InstagramScraper;
use Modules\Keyword\Entities\Keyword;
use Modules\Post\Entities\PostMedia;
use Storage;

class GetInstagramPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        private mixed $data,
        private string $name
    ) {
        $this->onQueue(Keyword::SOURCE_INSTAGRAM);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $medias = InstagramScraper::getPostDetail($this->data['post_id']);
        $medias = collect($medias);

        $data = [];

        foreach ($medias->toArray() as $media) {
            $url = $media['url'];
            $isVideo = $media['type'] !== 'image';
            $extension = $isVideo ? 'mp4' : 'jpg';

            $filePath = "instagram/{$this->name}/{$url}";
            $fileName = uploadFilename($filePath.'.'.$extension);

            if (! Storage::exists($fileName)) {
                dispatch(new DownloadMediaJob(
                    url: $url,
                    path: $fileName,
                ));
            }

            $data[] = [
                'post_id' => $this->postId,
                'type' => $isVideo ? 'video' : 'image',
                'path' => basename($fileName),
            ];
        }

        if (count($data)) {
            PostMedia::insert($data);
        }
    }
}
