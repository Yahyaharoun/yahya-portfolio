<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_categories', static function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->tinyText('description')->nullable();
            $table->string('cover_image', 300)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('media_gallery', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('media_category_id')
                  ->nullable()
                  ->constrained('media_categories')
                  ->nullOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->date('captured_at')->nullable()->index();
            $table->string('location', 200)->nullable()->index();
            $table->enum('type', ['photo', 'video'])->default('photo')->index();
            $table->string('filepath', 400);
            $table->string('thumbnail_path', 400)->nullable();
            $table->unsignedInteger('file_size')->nullable()->comment('Bytes');
            $table->string('mime_type', 80)->nullable();
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('duration_seconds')->nullable()->comment('For videos');
            $table->json('exif_data')->nullable()->comment('EXIF metadata stored as JSON');
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['media_category_id', 'type', 'is_visible']);
            $table->index(['type', 'is_featured', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_gallery');
        Schema::dropIfExists('media_categories');
    }
};
