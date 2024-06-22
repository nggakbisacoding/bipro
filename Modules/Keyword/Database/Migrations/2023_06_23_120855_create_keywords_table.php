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
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();
            $table->foreignUuid('batch_id')->nullable();
            $table->string('name')->index();
            $table->string('source');
            $table->string('type', 10)->default('keyword');
            $table->boolean('status')->default(1);
            $table->boolean('is_first')->default(1);
            $table->timestamp('last_post')->nullable();
            $table->timestamp('last_crawled')->nullable();
            $table->timestamp('since')->nullable();
            $table->timestamp('until')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

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
        Schema::dropIfExists('keywords');
    }
};
