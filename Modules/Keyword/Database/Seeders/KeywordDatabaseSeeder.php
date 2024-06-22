<?php

namespace Modules\Keyword\Database\Seeders;

use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;
use Modules\Keyword\Entities\Keyword;

class KeywordDatabaseSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        $this->truncate('keywords');

        if (app()->environment(['local', 'testing'])) {
            // Keyword::factory(2)->create();
        }

        $this->enableForeignKeys();
    }
}
