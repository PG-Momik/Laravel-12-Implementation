<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 40)->unique()->index();
            $table->integer('ord_in_thread')->nullable();
            $table->text('url')->nullable();
            $table->text('parent_url')->nullable();
            $table->text('author')->nullable();
            $table->text('title')->nullable();
            $table->text('language')->nullable();
            $table->text('sentiment')->nullable();
            $table->text('text')->nullable();
            $table->text('highlight_text')->nullable();
            $table->text('highlight_title')->nullable();
            $table->text('highlight_thread_title')->nullable();
            $table->float('rating')->nullable();
            $table->json('thread')->nullable();
            $table->json('social')->nullable();
            $table->json('categories')->nullable();
            $table->json('topics')->nullable();
            $table->json('external_links')->nullable();
            $table->json('external_images')->nullable();
            $table->json('trust')->nullable();
            $table->json('syndication')->nullable();
            $table->boolean('ai_allow')->nullable();
            $table->boolean('has_canonical')->nullable();
            $table->boolean('webz_reporter')->nullable();
            $table->boolean('breaking')->nullable();
            $table->timestamp('published')->nullable();
            $table->timestamp('crawled')->nullable();
            $table->timestamp('updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

