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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->string('title', 512)->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('url');
            $table->string('url_hash', 64)->unique();
            $table->string('url_to_image')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->string('source', 255)->nullable();
            $table->string('source_id', 255)->nullable();
            $table->string('author', 255)->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');

            $table->timestamps();

            $table->index('published_at');
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
