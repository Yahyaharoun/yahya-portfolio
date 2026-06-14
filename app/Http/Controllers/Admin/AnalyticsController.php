<?php

namespace App\Http\Controllers\Admin;

use App\Models\CvDownload;
use App\Models\Partnership;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AnalyticsController
{
    public function index()
    {
        // Stats par mois pour les CV downloads
        $cvByMonth = DB::table('cv_downloads')
            ->selectRaw("strftime('%Y-%m', created_at) as month, count(*) as total")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Stats par mois pour les partenariats
        $partnersByMonth = DB::table('partnerships')
            ->whereNull('deleted_at')
            ->selectRaw("strftime('%Y-%m', created_at) as month, count(*) as total")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Visites par page
        $pageVisits = DB::table('page_visits')
            ->select('path', DB::raw('SUM(visit_count) as total_visits'), DB::raw('SUM(unique_visitors) as unique_visitors'))
            ->groupBy('path')
            ->orderByDesc('total_visits')
            ->limit(20)
            ->get();

        // Motifs de demande CV
        $cvMotives = DB::table('cv_downloads')
            ->selectRaw("motive, count(*) as total")
            ->groupBy('motive')
            ->orderByDesc('total')
            ->get();

        return Inertia::render('Admin/Analytics/Index', [
            'cvByMonth' => $cvByMonth,
            'partnersByMonth' => $partnersByMonth,
            'pageVisits' => $pageVisits,
            'cvMotives' => $cvMotives,
            'totals' => [
                'cvDownloads' => CvDownload::count(),
                'partnerships' => Partnership::count(),
                'totalVisits' => DB::table('page_visits')->sum('visit_count'),
                'uniqueVisitors' => DB::table('page_visits')->sum('unique_visitors'),
            ]
        ]);
    }
}
