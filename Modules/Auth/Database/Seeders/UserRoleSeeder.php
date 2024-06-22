<?php

namespace Modules\Auth\Database\Seeders;

use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;

/**
 * Class UserRoleTableSeeder.
 */
class UserRoleSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        User::find(1)->assignRole(config('boilerplate.access.role.admin'));

        $this->enableForeignKeys();
    }
}
