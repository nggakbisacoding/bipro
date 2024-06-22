<?php

namespace Modules\Keyword\Tests\Feature\Backend;

use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class KeywordIndexTest extends TestCase
{
    public function test_admin_can_view_keyword_page()
    {
        $this->loginAsAdmin();
        
        $this->get(route('admin.keyword.index'))
            // ->assertSee('Keywords')
            ->assertInertia(fn (Assert $page) => $page
                ->component('keyword::backend.index', false)
                ->has('data', fn (Assert $page) => $page
                    ->has('data')
                    ->has('pagination', fn(Assert $page) => $page
                        ->where('current_page', 1)
                        ->where('per_page', 10)
                        ->hasAll(['total', 'per_page', 'current_page'])
                    )
                )
            );
    }
}
