<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cv_downloads', static function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email', 180)->index();
            $table->string('organization', 200)->nullable()->index();
            $table->enum('motive', [
                'recruitment',
                'partnership',
                'academic',
                'personal',
                'other',
            ])->default('other')->index();
            $table->ipAddress('ip_address')->nullable()->index();
            $table->string('user_agent', 300)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('country_code', 3)->nullable()->index();
            $table->string('cv_version', 20)->nullable()->comment('e.g. 2024-EN');
            $table->timestamps();

            $table->index(['motive', 'created_at']);
            $table->index(['organization', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_downloads');
    }
};
