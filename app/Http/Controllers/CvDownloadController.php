<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\ProcessCvLead;
use App\Models\CvDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

use App\Rules\NotDisposableEmail;
use App\Rules\ValidPhoneRegex;

/**
 * CvDownloadController
 *
 * Handles the lead capture, queues background processing, and streams the PDF
 * back to the client without a full page reload.
 */
class CvDownloadController
{
    /**
     * Process the incoming lead request and return the CV PDF as a streamed download.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function processDownload(Request $request): StreamedResponse
    {
        // Validate incoming data with strict typing and regex patterns.
        $validated = $request->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'phone'    => ['required', 'string', 'max:30', new ValidPhoneRegex],
            'email'    => ['required', 'string', 'email:rfc,dns', 'max:255', new NotDisposableEmail],
            'organization' => ['required', 'string', 'max:255'],
            'motive'   => ['required', 'string', 'max:500'],
        ]);

        // Dispatch the background job to persist the lead and fire notifications.
        ProcessCvLead::dispatch($validated)->onQueue('cv-leads');

        // Resolve the PDF file path – assuming it lives in storage/app/public/cv.
        $pdfPath = 'public/cv/Yahya_Haroun_CV.pdf';
        if (!Storage::exists($pdfPath)) {
            Log::error('CV PDF missing', ['path' => $pdfPath]);
            abort(404, 'CV file not found');
        }

        // Stream the PDF directly to the browser.
        return Response::streamDownload(function () use ($pdfPath) {
            $stream = Storage::readStream($pdfPath);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 'Yahya_Haroun_CV.pdf', [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
