<?php

namespace Modules\Keyword\Events;

use App\Exceptions\GeneralException;
use Illuminate\Queue\SerializesModels;
use Modules\Keyword\Entities\Keyword;

class KeywordCreated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public mixed $keyword)
    {
        if (! ($this->keyword instanceof Keyword) && ! is_array($this->keyword)) {
            throw new GeneralException('The $keyword parameter must be an instance of Keyword or an array.');
        }

        $this->keyword = $keyword;
    }
}
