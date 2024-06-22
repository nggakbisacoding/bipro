<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->morphs('postable');
            $table->string('post_id')->unique()->index();
            $table->string('name')->index();
            $table->string('username')->index();
            $table->string('avatar')->nullable();
            $table->text('message')->nullable();
            $table->text('hashtags')->nullable();
            $table->timestamp('date')->index();
            $table->json('stats')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
