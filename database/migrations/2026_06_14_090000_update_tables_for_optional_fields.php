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
        // Certifications: description optionnelle
        Schema::table('certifications', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });

        // Timeline: description, date_start, date_end optionnels + add thumbnail
        Schema::table('timeline_items', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->date('date_start')->nullable()->change();
            $table->date('date_end')->nullable()->change();
            if (!Schema::hasColumn('timeline_items', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('description');
            }
        });

        // Gallery: title optionnel
        Schema::table('media_gallery', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change();
        });

        Schema::table('timeline_items', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change();
            $table->date('date_start')->nullable(false)->change();
            $table->date('date_end')->nullable(false)->change();
            if (Schema::hasColumn('timeline_items', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }
        });

        Schema::table('media_gallery', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
        });
    }
};
