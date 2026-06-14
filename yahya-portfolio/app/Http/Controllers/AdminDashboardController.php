<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CvDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

/**
 * AdminDashboardController
 *
 * Provides KPI aggregation from Redis and MySQL and a memory‑efficient CSV export
 * of the CV download lead table. All routes are protected by auth + admin gate.
 */
class AdminDashboardController extends Controller
{
    /**
     * Constructor – apply auth and admin middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin']);
    }

    /**
     * Gather high‑level metrics for the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function metrics(Request $request)
    {
        try {
            // Unique visitors – tracked via Redis HyperLogLog "unique_visitors".
            $uniqueVisitors = (int) Redis::pfcount('unique_visitors');

            // Total CV downloads – incremented in TrackAnalytics middleware.
            $totalCvDownloads = (int) Redis::get('total_cv_downloads') ?? 0;

            // Business inquiries – stored in a Redis counter.
            $businessInquiries = (int) Redis::get('business_inquiries') ?? 0;

            // Outbound link clicks – aggregated per platform (WhatsApp, Phone).
            $outboundClicks = Redis::hgetall('outbound_clicks'); // e.g., ['whatsapp' => 12, 'phone' => 8]

            return response()->json([
                'unique_visitors'   => $uniqueVisitors,
                'total_cv_downloads'=> $totalCvDownloads,
                'business_inquiries' => $businessInquiries,
                'outbound_clicks'    => $outboundClicks,
            ]);
        } catch (\Throwable $e) {
            Log::error('AdminDashboard metrics error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to fetch metrics'], 500);
        }
    }

    /**
     * Export the complete CV download log as a CSV file using a streamed response.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCvDownloadsCsv(Request $request): StreamedResponse
    {
        $filename = 'cv_downloads_' . now()->format('Ymd_His') . '.csv';

        $callback = function () {
            // Open output stream.
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                Log::error('Failed to open php://output for CSV export');
                return;
            }

            // Write CSV header.
            fputcsv($handle, ['ID', 'Full Name', 'Phone', 'Email', 'Organization', 'Motive', 'Created At', 'Updated At']);

            // Chunk through the CvDownload table to keep memory usage low.
            CvDownload::query()->orderBy('id')->chunk(500, function ($records) use ($handle) {
                foreach ($records as $record) {
                    fputcsv($handle, [
                        $record->id,
                        $record->full_name,
                        $record->phone,
                        $record->email,
                        $record->organization,
                        $record->motive,
                        $record->created_at->toDateTimeString(),
                        $record->updated_at->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
