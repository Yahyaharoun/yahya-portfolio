<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ne pas tracker les routes admin, les API calls Inertia, et les méthodes non-GET
        $path = $request->path();
        if (
            $request->method() !== 'GET' ||
            str_starts_with($path, 'admin') ||
            str_starts_with($path, 'api') ||
            str_starts_with($path, '_') ||
            $path === 'up' ||
            str_starts_with($path, 'cv/') ||
            str_starts_with($path, 'download') ||
            str_starts_with($path, 'logout') ||
            in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['ico', 'js', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf', 'eot'])
        ) {
            return $next($request);
        }

        try {
            $path = $request->path();
            $ip = $request->ip();
            $today = now()->toDateString();
            $hour = now()->hour;
            
            $isUnique = false;
            
            // Vérification stricte de l'IP sur les dernières 24h
            $recentVisit = DB::table('visitors')
                ->where('ip_address', $ip)
                ->where('created_at', '>=', now()->subDay())
                ->first();

            // Si pas de visite dans les dernières 24h
            if (!$recentVisit) {
                // Sauf si c'est localhost, on ne compte qu'une fois globalement
                if ($ip === '127.0.0.1' || $ip === '::1') {
                    $hasLocalhost = DB::table('visitors')->where('ip_address', $ip)->exists();
                    if (!$hasLocalhost) {
                        $isUnique = true;
                        DB::table('visitors')->insert(['ip_address' => $ip, 'created_at' => now(), 'updated_at' => now()]);
                    }
                } else {
                    $isUnique = true;
                    DB::table('visitors')->insert(['ip_address' => $ip, 'created_at' => now(), 'updated_at' => now()]);
                }
            }

            $record = DB::table('page_visits')
                ->where('path', $path)
                ->where('tracked_date', $today)
                ->where('tracked_hour', $hour)
                ->first();

            if ($record) {
                DB::table('page_visits')->where('id', $record->id)->update([
                    'unique_visitors' => $isUnique ? DB::raw('unique_visitors + 1') : DB::raw('unique_visitors'),
                    'visit_count' => DB::raw('visit_count + 1'),
                ]);
            } else {
                DB::table('page_visits')->insert([
                    'path' => $path,
                    'tracked_date' => $today,
                    'tracked_hour' => $hour,
                    'unique_visitors' => $isUnique ? 1 : 0,
                    'visit_count' => 1,
                ]);
            }
        } catch (\Exception $e) {
            // Ignore error if table doesn't exist yet
        }

        return $next($request);
    }
}
