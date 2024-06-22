<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Auth\Middleware\RequirePassword;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\User;
use Modules\Auth\Http\Middleware\TwoFactorAuthenticationStatus;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed');
        Artisan::call('module:seed');
        
        config()->set('inertia.testing.page_paths', array_merge(
            config()->get('inertia.testing.page_paths', []),
            glob(__DIR__ . '/../Modules/*/Resources/pages', GLOB_ONLYDIR | GLOB_BRACE)
        ));

        $this->withoutMiddleware(RequirePassword::class);
        $this->withoutMiddleware(TwoFactorAuthenticationStatus::class);
    }

    protected function getAdminRole()
    {
        return Role::find(1);
    }

    protected function getMasterAdmin()
    {
        return User::find(1);
    }

    protected function loginAsAdmin($admin = false)
    {
        if (! $admin) {
            $admin = $this->getMasterAdmin();
        }

        $this->actingAs($admin);

        return $admin;
    }

    protected function logout()
    {
        return auth()->logout();
    }
}
