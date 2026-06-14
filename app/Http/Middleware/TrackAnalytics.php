<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

/**
 * TrackAnalytics Middleware
 *
 * Strategy:
 *   1. On each non-bot, non-asset request → atomically increment Redis counters.
 *   2. An hourly scheduled command (FlushAnalyticsToDatabase) upserts aggregated
 *      Redis data into MySQL (click_tracking / page_visits) and resets the counters.
 *
 * Redis key conventions
 * ─────────────────────
 *   clicks:{platform}:{YYYY-MM-DD}:{HH}:{CC}       → INCRBY integer
 *   visits:{path_hash}:{YYYY-MM-DD}:{HH}:{CC}:{DV} → INCRBY integer
 *   visitors:{path_hash}:{YYYY-MM-DD}               → PFADD HyperLogLog (unique)
 *   analytics:meta:{path_hash}                      → HSET {path, route}
 *
 * where CC = ISO-3166-1 alpha-2 country code (or "XX"), DV = device type.
 */
final class TrackAnalytics
{
    /** Bot user-agent fragment list (lowercase). */
    private const BOTS = [
        'bot', 'crawler', 'spider', 'slurp', 'baiduspider',
        'facebookexternalhit', 'twitterbot', 'rogerbot', 'linkedinbot',
        'embedly', 'quora link preview', 'showyoubot', 'outbrain',
        'pinterest', 'developers.google.com/+/web/snippet', 'lighthouse',
        'headlesschrome', 'google-inspectiontool', 'semrushbot',
        'ahrefsbot', 'mj12bot', 'dotbot',
    ];

    /** Static asset extensions to skip. */
    private const STATIC_EXTENSIONS = [
        'css', 'js', 'ico', 'png', 'jpg', 'jpeg', 'gif', 'webp',
        'svg', 'woff', 'woff2', 'ttf', 'eot', 'mp4', 'webm',
        'pdf', 'zip', 'map', 'json',
    ];

    /** Platform URL-pattern matching for click tracking (ordered by specificity). */
    private const CLICK_PLATFORMS = [
        'github'    => ['github.com'],
        'linkedin'  => ['linkedin.com'],
        'twitter'   => ['twitter.com', 'x.com'],
        'email'     => ['mailto:', '/contact'],
        'cv'        => ['/cv', '/resume'],
        'portfolio' => ['/projects'],
        'media'     => ['/media', '/gallery'],
    ];

    /** Redis key TTL: 25 hours so data survives past the hourly flush window. */
    private const REDIS_TTL_SECONDS = 90_000;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Fire-and-forget: do not block the response.
        // In production replace with a queued job for true async.
        if ($this->shouldTrack($request)) {
            try {
                $this->recordVisit($request);
                $this->recordClick($request);
            } catch (\Throwable $e) {
                // Analytics must NEVER break the application.
                Log::warning('[TrackAnalytics] Redis write failed', [
                    'error' => $e->getMessage(),
                    'path'  => $request->path(),
                ]);
            }
        }

        return $response;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Internal helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function shouldTrack(Request $request): bool
    {
        // Skip non-GET requests (forms, API mutations, etc.)
        if (! $request->isMethod('GET')) {
            return false;
        }

        // Skip static assets
        $ext = strtolower((string) pathinfo($request->path(), PATHINFO_EXTENSION));
        if ($ext !== '' && \in_array($ext, self::STATIC_EXTENSIONS, true)) {
            return false;
        }

        // Skip admin panel routes
        if (str_starts_with($request->path(), 'admin')) {
            return false;
        }

        // Skip bot user agents
        $ua = strtolower((string) $request->userAgent());
        foreach (self::BOTS as $fragment) {
            if (str_contains($ua, $fragment)) {
                return false;
            }
        }

        return true;
    }

    private function recordVisit(Request $request): void
    {
        $date       = now()->toDateString();                      // YYYY-MM-DD
        $hour       = (int) now()->format('H');                   // 0-23
        $country    = $this->resolveCountry($request);            // ISO-2 or XX
        $device     = $this->resolveDevice($request);             // desktop|tablet|mobile
        $path       = '/'.ltrim($request->path(), '/');
        $pathHash   = substr(sha1($path), 0, 12);
        $routeName  = (string) ($request->route()?->getName() ?? '');

        // Counter key: visits:{hash}:{date}:{hour}:{country}:{device}
        $counterKey = "visits:{$pathHash}:{$date}:{$hour}:{$country}:{$device}";

        // HyperLogLog key for unique visitor estimation (day-level)
        $hllKey = "visitors:{$pathHash}:{$date}";

        // Visitor fingerprint (hashed to avoid storing PII)
        $fingerprint = sha1(
            $request->ip()
            .($request->userAgent() ?? '')
            .$date
        );

        Redis::pipeline(function ($pipe) use (
            $counterKey,
            $hllKey,
            $fingerprint,
            $pathHash,
            $path,
            $routeName,
        ): void {
            // Increment visit counter
            $pipe->incr($counterKey);
            $pipe->expire($counterKey, self::REDIS_TTL_SECONDS);

            // Track unique visitors via HyperLogLog (≤ 0.81% error, O(1) memory)
            $pipe->pfadd($hllKey, [$fingerprint]);
            $pipe->expire($hllKey, self::REDIS_TTL_SECONDS);

            // Store path metadata for the flush command to read
            $metaKey = "analytics:meta:{$pathHash}";
            $pipe->hset($metaKey, 'path', $path, 'route', $routeName);
            $pipe->expire($metaKey, self::REDIS_TTL_SECONDS);

            // Add counter key to a master set so the flush command can iterate
            $pipe->sadd('analytics:visit_keys', $counterKey);
        });
    }

    private function recordClick(Request $request): void
    {
        $platform = $this->resolvePlatform($request);

        if ($platform === null) {
            return;
        }

        $date    = now()->toDateString();
        $hour    = (int) now()->format('H');
        $country = $this->resolveCountry($request);

        $clickKey = "clicks:{$platform}:{$date}:{$hour}:{$country}";

        Redis::pipeline(function ($pipe) use ($clickKey): void {
            $pipe->incr($clickKey);
            $pipe->expire($clickKey, self::REDIS_TTL_SECONDS);
            $pipe->sadd('analytics:click_keys', $clickKey);
        });
    }

    private function resolvePlatform(Request $request): ?string
    {
        $fullUrl = strtolower($request->fullUrl());
        $referer = strtolower((string) $request->header('Referer', ''));

        foreach (self::CLICK_PLATFORMS as $platform => $patterns) {
            foreach ($patterns as $pattern) {
                if (
                    str_contains($fullUrl, $pattern)
                    || str_contains($referer, $pattern)
                ) {
                    return $platform;
                }
            }
        }

        return null;
    }

    private function resolveCountry(Request $request): string
    {
        // Populated by Cloudflare (CF-IPCountry) or similar CDN header.
        $cf = $request->header('CF-IPCountry');
        if (is_string($cf) && strlen($cf) === 2 && ctype_alpha($cf)) {
            return strtoupper($cf);
        }

        // Fallback: try X-Country-Code (set by a load balancer / geo-IP layer)
        $custom = $request->header('X-Country-Code');
        if (is_string($custom) && strlen($custom) === 2 && ctype_alpha($custom)) {
            return strtoupper($custom);
        }

        return 'XX'; // Unknown
    }

    private function resolveDevice(Request $request): string
    {
        $ua = strtolower((string) $request->userAgent());

        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }

        if (
            str_contains($ua, 'mobile')
            || str_contains($ua, 'android')
            || str_contains($ua, 'iphone')
        ) {
            return 'mobile';
        }

        return 'desktop';
    }
}
