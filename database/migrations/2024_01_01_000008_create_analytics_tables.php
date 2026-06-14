<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // click_tracking: aggregated MySQL table (hourly flush from Redis)
        Schema::create('click_tracking', static function (Blueprint $table): void {
            $table->id();
            $table->string('target_platform', 80)->index()->comment('e.g. github, linkedin, email, cv_download');
            $table->unsignedBigInteger('click_count')->default(0);
            $table->date('tracked_date')->index()->comment('Day-level granularity for daily aggregations');
            $table->unsignedTinyInteger('tracked_hour')->default(0)->comment('Hour 0-23 for hourly aggregations');
            $table->string('country_code', 3)->nullable()->index();
            $table->timestamp('last_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();

            $table->unique(['target_platform', 'tracked_date', 'tracked_hour', 'country_code'], 'click_tracking_unique');
            $table->index(['target_platform', 'tracked_date']);
        });

        // page_visits: separate table for visitor/session analytics
        Schema::create('page_visits', static function (Blueprint $table): void {
            $table->id();
            $table->string('route_name', 100)->nullable()->index()->comment('Named route or URI segment');
            $table->string('path', 300)->index();
            $table->unsignedBigInteger('visit_count')->default(0);
            $table->unsignedBigInteger('unique_visitors')->default(0);
            $table->date('tracked_date')->index();
            $table->unsignedTinyInteger('tracked_hour')->default(0);
            $table->string('country_code', 3)->nullable()->index();
            $table->string('device_type', 20)->nullable()->comment('desktop, tablet, mobile');
            $table->timestamp('last_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();

            $table->unique(
                ['path', 'tracked_date', 'tracked_hour', 'country_code', 'device_type'],
                'page_visits_unique'
            );
            $table->index(['path', 'tracked_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_visits');
        Schema::dropIfExists('click_tracking');
    }
};
