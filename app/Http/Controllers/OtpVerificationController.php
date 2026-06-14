<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Services\TwilioSmsService;

class OtpVerificationController extends Controller
{
    /**
     * Simule l'envoi d'un OTP par Email ou SMS.
     * Accessible via POST /api/otp/send
     */
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|email|nullable',
            'phone' => 'required_without:email|string|nullable',
        ]);

        $identifier = $request->email ?? $request->phone;
        
        // Générer un code à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Stocker en cache pour 10 minutes
        Cache::put("otp_{$identifier}", $code, now()->addMinutes(10));

        // Envoi de l'email avec la classe Mailable
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            \Illuminate\Support\Facades\Mail::to($identifier)->send(new \App\Mail\OtpMail($code));
        } else {
            // Si c'est un numéro de téléphone, on utilise Twilio
            $smsService = new TwilioSmsService();
            $smsService->sendOtp($identifier, $code);
            \Log::info("Code OTP SMS envoyé (ou tenté) via Twilio pour {$identifier}");
        }

        return response()->json([
            'message' => 'Code envoyé avec succès. (Checkez les logs en mode local)',
            'identifier' => $identifier
        ]);
    }

    /**
     * Vérifie le code OTP et crée un cookie de session validée.
     * Accessible via POST /api/otp/verify
     */
    public function verify(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $cachedCode = Cache::get("otp_{$request->identifier}");

        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json(['error' => 'Code invalide ou expiré.'], 403);
        }

        // OTP valide, on supprime du cache
        Cache::forget("otp_{$request->identifier}");

        // Créer un token PWA offline (expire dans 30 jours)
        $token = Str::random(64);
        
        // Stocker le token en base ou cache pour validation serveur
        Cache::put("validated_session_{$token}", $request->identifier, now()->addDays(30));

        // Définir un cookie sécurisé
        $cookie = cookie('otp_validated', $token, 60 * 24 * 30, null, null, env('SESSION_SECURE_COOKIE', false), true);

        return response()->json([
            'message' => 'Identité validée avec succès.',
            'token' => $token
        ])->withCookie($cookie);
    }
}
