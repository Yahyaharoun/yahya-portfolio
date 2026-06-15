<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\CvDownload;
use App\Notifications\NewCvDownload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * ProcessCvLead Job
 *
 * Handles persistence of lead data and dispatches notification jobs.
 */
class ProcessCvLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $fullName;
    public string $phone;
    public string $email;
    public string $organization;
    public string $motive;

    /**
     * Create a new job instance.
     */
    public function __construct(array $payload)
    {
        $this->fullName = $payload['fullName'] ?? $payload['name'] ?? 'Inconnu';
        $this->phone = $payload['phone'] ?? '';
        $this->email = $payload['email'] ?? '';
        $this->organization = $payload['organization'] ?? 'N/A';
        $this->motive = $payload['motive'] ?? '';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Persist the lead.
            CvDownload::create([
                'full_name' => $this->fullName,
                'phone' => $this->phone,
                'email' => $this->email,
                'organization' => $this->organization,
                'motive' => $this->motive,
            ]);

            // Dispatch a notification (e.g., to Slack or email).
            // Assuming a notifiable admin user exists.
            $admin = \App\Models\User::where('role', 'admin')->first();
            if ($admin) {
                $admin->notify(new NewCvDownload([
                    'name' => $this->fullName,
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'organization' => $this->organization,
                    'motive' => $this->motive,
                ]));
            }
        } catch (\Throwable $e) {
            // Log the error; the job can be retried automatically.
            Log::error('Failed to process CV lead', ['error' => $e->getMessage(), 'payload' => $this->toArray()]);
            // Optionally rethrow to trigger retry based on queue config.
            throw $e;
        }
    }

    /**
     * Convert the job data to an array for logging.
     */
    private function toArray(): array
    {
        return [
            'fullName' => $this->fullName,
            'phone' => $this->phone,
            'email' => $this->email,
            'organization' => $this->organization,
            'motive' => $this->motive,
        ];
    }
}
