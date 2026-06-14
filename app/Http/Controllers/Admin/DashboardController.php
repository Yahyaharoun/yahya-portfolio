<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\CvDownload;
use App\Models\Partnership;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function index()
    {
        // Comptage réel des visiteurs uniques (IPs uniques, hors localhost)
        $visitors = DB::table('visitors')
            ->where('ip_address', '!=', '127.0.0.1')
            ->where('ip_address', '!=', '::1')
            ->count();

        // Nouvelles notifications non lues
        $lastPartnershipsView = cache()->get('last_contracts_view', now()->subDays(30));
        $newPartnerships = Partnership::where('created_at', '>', $lastPartnershipsView)->count();
        $lastCvDownloadsView = cache()->get('last_cv_downloads_view', now()->subDays(30));
        $newCvDownloads = CvDownload::where('created_at', '>', $lastCvDownloadsView)->count();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'visitors' => (int) $visitors,
                'cvDownloads' => CvDownload::count(),
                'contracts' => Partnership::count(),
                'contacts' => $newPartnerships,
            ],
            'cvRequests' => CvDownload::latest()->take(10)->get(),
            'partnerships' => Partnership::latest()->take(20)->get(),
            'notifications' => [
                'newPartnerships' => $newPartnerships,
                'newCvDownloads' => $newCvDownloads,
                'total' => $newPartnerships + $newCvDownloads,
            ],
        ]);
    }
}
