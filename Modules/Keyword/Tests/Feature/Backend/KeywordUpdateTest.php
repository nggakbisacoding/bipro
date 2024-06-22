<?php

namespace Modules\Keyword\Tests\Feature\Backend;

use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Modules\Keyword\Entities\Keyword;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Keyword\Events\KeywordDeleted;
use Modules\Keyword\Events\KeywordUpdated;

class KeywordUpdateTest extends TestCase
{
    public function test_admin_can_access_edit_keyword_page()
    {
        $this->loginAsAdmin();

        $keyword = Keyword::factory()->create();
        
        $this->get(route('admin.keyword.edit', ['keyword' => $keyword->id]))
            ->assertInertia(fn (Assert $page) => $page
                ->component('keyword::backend.edit', false)
                // ->has('podcast', fn (Assert $page) => $page
                //     ->where('id', $podcast->id)
                //     ->where('subject', 'The Laravel Podcast')
                //     ->where('description', 'The Laravel Podcast brings you Laravel and PHP development news and discussion.')
                //     ->has('seasons', 4)
                //     ->has('seasons.4.episodes', 21)
                //     ->has('host', fn (Assert $page) => $page
                //         ->where('id', 1)
                //         ->where('name', 'Matt Stauffer')
                //     )
                //     ->has('subscribers', 7, fn (Assert $page) => $page
                //         ->where('id', 2)
                //         ->where('name', 'Claudio Dekker')
                //         ->where('platform', 'Apple Podcasts')
                //         ->etc()
                //         ->missing('email')
                //         ->missing('password')
                //     )
                // )
            );
    }

    public function test_admin_can_update_keyword()
    {
        Event::fake();

        $this->loginAsAdmin();

        $keyword = Keyword::factory()->create();

        $response = $this->put(route('admin.keyword.update', ['keyword' => $keyword->id]), [
            'name' => 'Test User',
            'status' => 1,
            'source' => Keyword::SOURCE_FACEBOOK,
            'type' => Keyword::TYPE_KEYWORD,
        ]);

        $this->assertDatabaseHas(
            'keywords',
            [
                'name' => 'Test User',
                'status' => 1,
                'source' => Keyword::SOURCE_FACEBOOK,
                'type' => Keyword::TYPE_KEYWORD,
            ]
        );

        $response->assertSessionHas(['flash_success' => __('Keyword updated successfully')]);

        Event::assertDispatched(KeywordUpdated::class);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.keyword.index'));

    }
    
    public function test_admin_can_delete_keyword()
    {
        Event::fake();

        $this->loginAsAdmin();

        $keyword = Keyword::factory()->create();

        $response = $this->delete(route('admin.keyword.destroy', ['keyword' => $keyword->id]));

        $this->assertSoftDeleted('keywords', [
            'id' => $keyword->id,
        ]);

        $response->assertSessionHas(['flash_success' => __('Keyword deleted successfully')]);

        Event::assertDispatched(KeywordDeleted::class);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.keyword.index'));
    }
}
