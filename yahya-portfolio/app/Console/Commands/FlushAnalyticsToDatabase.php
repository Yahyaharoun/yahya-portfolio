<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * FlushAnalyticsToDatabase
 *
 * Runs every hour via the scheduler. Drains all Redis analytics counters
 * into MySQL using INSERT ... ON DUPLICATE KEY UPDATE for idempotency.
 *
 * Schedule registration (in bootstrap/app.php or routes/console.php):
 *   Schedule::command(FlushAnalyticsToDatabase::class)->hourly()->withoutOverlapping();
 */
final class FlushAnalyticsToDatabase extends Command
{
    protected $signature   = 'analytics:flush';
    protected $description = 'Flush Redis analytics counters to MySQL (hourly batch aggregation).';

    public function handle(): int
    {
        $this->flushClickTracking();
        $this->flushPageVisits();

        $this->info('[analytics:flush] Completed at '.now()->toDateTimeString());

        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────────────────────────────────

    private function flushClickTracking(): void
    {
        /** @var array<int, string> $keys */
        $keys = Redis::smembers('analytics:click_keys');

        if (empty($keys)) {
            return;
        }

        $rows = [];

        foreach ($keys as $key) {
            // Key format: clicks:{platform}:{date}:{hour}:{country}
            $parts = explode(':', $key, 5);

            if (count($parts) !== 5) {
                Log::warning('[analytics:flush] Malformed click key skipped', ['key' => $key]);
                continue;
            }

            [, $platform, $date, $hour, $country] = $parts;

            // Atomic GET + DELETE to avoid double-counting across concurrent flushes
            $count = (int) Redis::getdel($key);

            if ($count <= 0) {
                Redis::srem('analytics:click_keys', $key);
                continue;
            }

            $rows[] = [
                'target_platform' => $platform,
                'tracked_date'    => $date,
                'tracked_hour'    => (int) $hour,
                'country_code'    => $country === 'XX' ? null : $country,
                'click_count'     => $count,
                'last_updated_at' => now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            Redis::srem('analytics:click_keys', $key);
        }

        if (! empty($rows)) {
            DB::table('click_tracking')->upsert(
                $rows,
                ['target_platform', 'tracked_date', 'tracked_hour', 'country_code'],
                ['click_count' => DB::raw('click_count + VALUES(click_count)'), 'last_updated_at', 'updated_at'],
            );
        }
    }

    private function flushPageVisits(): void
    {
        /** @var array<int, string> $keys */
        $keys = Redis::smembers('analytics:visit_keys');

        if (empty($keys)) {
            return;
        }

        $rows = [];

        foreach ($keys as $key) {
            // Key format: visits:{hash}:{date}:{hour}:{country}:{device}
            $parts = explode(':', $key, 6);

            if (count($parts) !== 6) {
                Log::warning('[analytics:flush] Malformed visit key skipped', ['key' => $key]);
                continue;
            }

            [, $pathHash, $date, $hour, $country, $device] = $parts;

            $count = (int) Redis::getdel($key);

            if ($count <= 0) {
                Redis::srem('analytics:visit_keys', $key);
                continue;
            }

            // Resolve path + route from metadata hash
            $meta      = Redis::hgetall("analytics:meta:{$pathHash}");
            $path      = $meta['path']  ?? "/{$pathHash}";
            $routeName = $meta['route'] ?? null;

            // Unique visitor count from HyperLogLog
            $uniqueVisitors = (int) Redis::pfcount("visitors:{$pathHash}:{$date}");

            $rows[] = [
                'route_name'     => $routeName ?: null,
                'path'           => $path,
                'visit_count'    => $count,
                'unique_visitors'=> $uniqueVisitors,
                'tracked_date'   => $date,
                'tracked_hour'   => (int) $hour,
                'country_code'   => $country === 'XX' ? null : $country,
                'device_type'    => $device,
                'last_updated_at'=> now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            Redis::srem('analytics:visit_keys', $key);
        }

        if (! empty($rows)) {
            DB::table('page_visits')->upsert(
                $rows,
                ['path', 'tracked_date', 'tracked_hour', 'country_code', 'device_type'],
                [
                    'visit_count'     => DB::raw('visit_count + VALUES(visit_count)'),
                    'unique_visitors' => DB::raw('VALUES(unique_visitors)'),
                    'last_updated_at',
                    'updated_at',
                ],
            );
        }
    }
}
