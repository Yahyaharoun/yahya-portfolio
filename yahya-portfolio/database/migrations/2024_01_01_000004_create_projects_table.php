<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tags', static function (Blueprint $table): void {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('slug', 60)->unique();
            $table->timestamps();
        });

        Schema::create('projects', static function (Blueprint $table): void {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description');
            $table->mediumText('long_description')->nullable();
            $table->json('tech_stack')->comment('Ordered array of technology names');
            $table->json('screenshots')->comment('Array of {url, caption, order} objects');
            $table->string('github_url', 400)->nullable();
            $table->string('demo_url', 400)->nullable();
            $table->string('thumbnail', 300)->nullable();
            $table->enum('status', ['realized', 'future', 'in_progress', 'archived'])
                  ->default('realized')
                  ->index();
            $table->enum('visibility', ['public', 'private'])->default('public')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_featured', 'visibility']);
            $table->index(['is_featured', 'sort_order']);
            $table->fullText(['title', 'description']);
        });

        Schema::create('project_tag', static function (Blueprint $table): void {
            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->cascadeOnDelete();
            $table->foreignId('project_tag_id')
                  ->constrained('project_tags')
                  ->cascadeOnDelete();
            $table->primary(['project_id', 'project_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tag');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_tags');
    }
};
