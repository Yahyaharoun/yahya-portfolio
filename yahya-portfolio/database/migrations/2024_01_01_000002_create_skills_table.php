<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_categories', static function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('slug', 80)->unique();
            $table->string('icon', 100)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('skills', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('skill_category_id')
                  ->constrained('skill_categories')
                  ->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])
                  ->default('intermediate')
                  ->index();
            $table->tinyText('description')->nullable();
            $table->string('icon', 150)->nullable();
            $table->unsignedTinyInteger('proficiency')->default(0)->comment('0-100 percentage');
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['skill_category_id', 'level', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_categories');
    }
};
