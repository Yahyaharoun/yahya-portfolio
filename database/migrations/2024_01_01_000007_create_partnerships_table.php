<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partnerships', static function (Blueprint $table): void {
            $table->id();
            $table->string('company', 200)->index();
            $table->string('logo_path', 300)->nullable();
            $table->string('website', 400)->nullable();
            $table->string('type')->default('other')->index();
            $table->string('contact_name', 150)->nullable();
            $table->string('contact_email', 180)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->text('message');
            $table->enum('status', ['new', 'in_progress', 'treated', 'rejected'])
                  ->default('new')
                  ->index();
            $table->text('admin_notes')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('treated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'type', 'created_at']);
            $table->index(['company', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partnerships');
    }
};
