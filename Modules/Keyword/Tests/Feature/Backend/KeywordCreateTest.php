<?php

namespace Modules\Keyword\Tests\Feature\Backend;

use Event;
use Tests\TestCase;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Events\KeywordCreated;
use Inertia\Testing\AssertableInertia as Assert;

class KeywordCreateTest extends TestCase
{
    public function test_admin_can_view_create_keyword_page()
    {
        $this->loginAsAdmin();
        
        $this->get(route('admin.keyword.create'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('keyword::backend.create', false)
            );
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_admin_can_create_new_keyword()
    {
        Event::fake();

        $this->loginAsAdmin();

        $response = $this->post(route('admin.keyword.store'), [
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

        $response->assertSessionHas(['flash_success' => __('Keyword created successfully')]);

        Event::assertDispatched(KeywordCreated::class);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.keyword.index'));
    }

    public function test_create_new_keyword_require_validation()
    {
        $this->loginAsAdmin();

        $response = $this->post(route('admin.keyword.store'));
        $response->assertSessionHasErrors(['name', 'source']);
    }

    public function test_create_new_keyword_failed_with_invalid_source()
    {
        $this->loginAsAdmin();

        $response = $this->post(route('admin.keyword.store'), [
            'name' => 'Test User',
            'status' => 1,
            'source' => 'invalid_source',
            'type' => Keyword::TYPE_KEYWORD,
        ]);

        $response->assertSessionHasErrors(['source']);
    }

    public function test_create_new_keyword_failed_with_invalid_type()
    {
        $this->loginAsAdmin();

        $response = $this->post(route('admin.keyword.store'), [
            'name' => 'Test User',
            'status' => 1,
            'source' => Keyword::SOURCE_FACEBOOK,
            'type' => 'invalid type',
        ]);

        $response->assertSessionHasErrors(['type']);
    }
}
