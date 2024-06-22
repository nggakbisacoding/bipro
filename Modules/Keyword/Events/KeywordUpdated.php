<?php

namespace Modules\Keyword\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Keyword\Entities\Keyword;

class KeywordUpdated
{
    use SerializesModels;

    public $afterCommit = true;

    public Keyword $keyword;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Keyword $keyword)
    {
        $this->keyword = $keyword;
    }
}
