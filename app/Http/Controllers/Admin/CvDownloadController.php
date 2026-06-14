<?php

namespace App\Http\Controllers\Admin;

use App\Models\CvDownload;
use Inertia\Inertia;

class CvDownloadController
{
    public function index()
    {
        cache()->put('last_cv_downloads_view', now());
        
        return Inertia::render('Admin/CvDownloads/Index', [
            'downloads' => CvDownload::latest()->get()
        ]);
    }

    public function destroy(CvDownload $cvDownload)
    {
        $cvDownload->delete();
        return back()->with('success', 'Entrée supprimée.');
    }
}
