<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Partnership;
use App\Models\CvDownload;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Inject real-time notification counts on every admin page
        $notifications = null;
        if ($request->is('admin*') && auth()->check()) {
            $lastPartnershipsView = cache()->get('last_contracts_view', now()->subDays(30));
            $newPartnerships = Partnership::where('created_at', '>', $lastPartnershipsView)->count();
            $lastCvDownloadsView = cache()->get('last_cv_downloads_view', now()->subDays(30));
            $newCvDownloads  = CvDownload::where('created_at', '>', $lastCvDownloadsView)->count();
            $notifications = [
                'newPartnerships' => $newPartnerships,
                'newCvDownloads'  => $newCvDownloads,
                'total'           => $newPartnerships + $newCvDownloads,
            ];
        }

        return array_merge(parent::share($request), [
            'notifications' => $notifications,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
        ]);
    }
}
