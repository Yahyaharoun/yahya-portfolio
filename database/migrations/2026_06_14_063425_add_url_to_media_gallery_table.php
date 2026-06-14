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
        Schema::table('media_gallery', function (Blueprint $table) {
            $table->string('url', 400)->nullable()->after('filepath');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_gallery', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
};
