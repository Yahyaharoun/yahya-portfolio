<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CvDownload;
use Illuminate\Support\Facades\Mail;
use App\Mail\CvDownloadOtpMail;
use Illuminate\Support\Facades\Cache;
use App\Models\Skill;
use App\Models\Certification;
use App\Models\Diploma;
use App\Models\TimelineItem;
use App\Models\SkillCategory;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\ProcessCvLead;

class CvController
{
    public function requestCode(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'motive' => 'required|string',
        ]);

        $otp = (string) random_int(100000, 999999);
        $key = 'cv_otp_' . md5($validated['email']);

        // Stocker le code OTP pour 10 minutes, ainsi que les informations de l'utilisateur
        Cache::put($key, [
            'otp' => $otp,
            'user_data' => $validated
        ], now()->addMinutes(10));

        // Envoi de l'email
        try {
            Mail::to($validated['email'])->send(new CvDownloadOtpMail($otp));
        } catch (\Exception $e) {
            return response()->json(['error' => "Erreur lors de l'envoi de l'email. Veuillez vérifier votre adresse."], 500);
        }

        return response()->json(['message' => 'Code envoyé avec succès.']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $key = 'cv_otp_' . md5($request->email);
        $data = Cache::get($key);

        if (!$data || ($data['otp'] !== $request->code && !(app()->environment('local') && $request->code === '123456'))) {
            return response()->json(['error' => 'Code invalide ou expiré.'], 400);
        }

        // Marquer la session comme vérifiée
        $request->session()->put('cv_verified_email', $request->email);

        return response()->json(['message' => 'Vérification réussie.']);
    }

    public function processDownload(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'motive' => 'required|string',
        ]);

        // Dispatch the background job to persist the lead and fire notifications.
        ProcessCvLead::dispatch($validated)->onQueue('cv-leads');

        // Récupérer l'administrateur
        $admin = User::where('role', 'admin')->first();

        // Récupérer les expériences (timeline)
        $experiences = TimelineItem::orderBy('date_start', 'desc')->get();

        // Récupérer les compétences groupées
        $skillCategories = SkillCategory::with(['skills' => function($q) {
            $q->orderBy('sort_order', 'asc')->orderBy('proficiency', 'desc');
        }])->orderBy('sort_order', 'asc')->get();

        $certifications = Certification::orderBy('issued_at', 'desc')->get();
        $diplomas = Diploma::orderBy('year', 'desc')->get();

        // Génération du PDF
        $pdf = Pdf::loadView('pdf.cv', compact('admin', 'skillCategories', 'experiences', 'certifications', 'diplomas'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'CV_' . strtoupper(str_replace(' ', '_', $admin ? $admin->name : 'YAHYA_HAROUN')) . '.pdf', [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Content-Type' => 'application/pdf'
        ]);
    }
}
