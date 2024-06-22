<?php

namespace Modules\Auth\Database\Seeders;

use Modules\Auth\Entities\Role;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\User;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Create Roles
        Role::create([
            'id' => 1,
            'type' => User::TYPE_ADMIN,
            'name' => 'Administrator',
        ]);

        Role::create([
            'id' => 2,
            'type' => User::TYPE_USER,
            'name' => 'User',
        ]);

        // Non Grouped Permissions
        //

        // Grouped permissions
        // Users category
        $users = Permission::create([
            'type' => User::TYPE_ADMIN,
            'module' => 'Users',
            'name' => 'admin.access.user',
            'description' => 'All User Permissions',
        ]);

        $users->children()->saveMany([
            new Permission([
                'type' => User::TYPE_ADMIN,
                'module' => 'Users',
                'name' => 'admin.access.user.list',
                'description' => 'View Users',
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'module' => 'Users',
                'name' => 'admin.access.user.deactivate',
                'description' => 'Deactivate Users',
                'sort' => 2,
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'module' => 'Users',
                'name' => 'admin.access.user.reactivate',
                'description' => 'Reactivate Users',
                'sort' => 3,
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'module' => 'Users',
                'name' => 'admin.access.user.clear-session',
                'description' => 'Clear User Sessions',
                'sort' => 4,
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'module' => 'Users',
                'name' => 'admin.access.user.impersonate',
                'description' => 'Impersonate Users',
                'sort' => 5,
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'module' => 'Users',
                'name' => 'admin.access.user.change-password',
                'description' => 'Change User Passwords',
                'sort' => 6,
            ]),
        ]);

        // Assign Permissions to other Roles
        //

        $this->enableForeignKeys();
    }
}
