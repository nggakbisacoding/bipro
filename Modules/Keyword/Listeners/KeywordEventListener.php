<?php

namespace Modules\Keyword\Listeners;

use Illuminate\Events\Dispatcher;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Events\KeywordCreated;
use Modules\Keyword\Events\KeywordDeleted;
use Modules\Keyword\Events\KeywordUpdated;
use Modules\Keyword\Jobs\Instagram\ScrapeInstagramImportedJob;
use Modules\Keyword\Jobs\Instagram\ScrapeInstagramJob;
use Modules\Keyword\Jobs\Tiktok\ScrapeTiktokImportedJob;
use Modules\Keyword\Jobs\Tiktok\ScrapeTiktokJob;
use Modules\Keyword\Jobs\Twitter\ScrapeTwitterImportedJob;
use Modules\Keyword\Jobs\Twitter\ScrapeTwitterJob;
use Modules\Post\Entities\Post;

class KeywordEventListener
{
    public function onCreated(KeywordCreated $event)
    {
        if (is_array($event->keyword)) {
            $this->onBulkCreated($event->keyword);

            return;
        }

        if (isset($event->keyword->status) && $event->keyword->status) {
            $this->scrapeKeyword($event->keyword);
        }

        activity('keyword')
            ->performedOn($event->keyword)
            ->withProperties([
                'keyword' => [
                    'username' => $event->keyword->name,
                    'name' => $event->keyword->name,
                    'source' => $event->keyword->source,
                ],
            ])
            ->log(':causer.name created keyword :subject.name from source: :subject.source');
    }

    private function onBulkCreated($keywords)
    {
        $keywords = collect($keywords);
        $twitter = $keywords->filter(fn ($keyword) => $keyword['source'] === Keyword::SOURCE_TWITTER);
        $instagram = $keywords->filter(fn ($keyword) => $keyword['source'] === Keyword::SOURCE_INSTAGRAM);
        $tiktok = $keywords->filter(fn ($keyword) => $keyword['source'] === Keyword::SOURCE_TIKTOK);

        if ($twitter->count()) {
            dispatch(new ScrapeTwitterImportedJob($twitter));
        }
        if ($instagram->count()) {
            dispatch(new ScrapeInstagramImportedJob($instagram));
        }
        if ($tiktok->count()) {
            dispatch(new ScrapeTiktokImportedJob($tiktok));
        }

        activity('keyword')
            ->withProperties([
                'total' => $keywords->count(),
            ])
            ->log(':causer.name imported :subject.total keywords');
    }

    public function onUpdated($event)
    {
        activity('keyword')
            ->performedOn($event->keyword)
            ->withProperties([
                'keyword' => [
                    'username' => $event->keyword->name,
                    'name' => $event->keyword->name,
                    'source' => $event->keyword->source,
                ],
            ])
            ->log(':causer.name updated keyword :subject.name from source: :subject.source');
    }

    public function onDeleted($event)
    {
        activity('keyword')
            ->performedOn($event->keyword)
            ->log(':causer.name deleted keyword :subject.name');

        Post::wherePostableId($event->keyword->id)->delete();
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            KeywordCreated::class => 'onCreated',
            KeywordUpdated::class => 'onUpdated',
            KeywordDeleted::class => 'onDeleted',
        ];
    }

    private function scrapeKeyword(Keyword $keyword)
    {
        switch ($keyword->source) {
            case Keyword::SOURCE_TWITTER:
                dispatch(new ScrapeTwitterJob($keyword));
                break;

            case Keyword::SOURCE_INSTAGRAM:
                dispatch(new ScrapeInstagramJob($keyword));
                break;

            default:
                dispatch(new ScrapeTiktokJob($keyword));
                break;
        }
    }
}
