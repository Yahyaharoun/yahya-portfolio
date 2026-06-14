<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', static function (Blueprint $table): void {
            $table->id();
            $table->string('title', 200);
            $table->string('organization', 150)->index();
            $table->string('credential_id', 150)->nullable();
            $table->date('issued_at');
            $table->date('expires_at')->nullable();
            $table->string('image_path', 300)->nullable();
            $table->string('verification_url', 500)->nullable();
            $table->enum('type', ['certification', 'diploma', 'course', 'award'])
                  ->default('certification')
                  ->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_featured', 'issued_at']);
            $table->index(['organization', 'issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
