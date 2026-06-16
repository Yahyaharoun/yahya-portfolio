<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Services\TwilioSmsService;

class OtpVerificationController
{
    /**
     * Simule l'envoi d'un OTP par Email ou SMS.
     * Accessible via POST /api/otp/send
     */
    public function send(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required_without:phone|email|nullable',
            'phone' => 'required_without:email|string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        $identifier = $request->email ?? $request->phone;
        
        $sent = false;
        $apiErrors = [];

        try {
            // Générer un code à 6 chiffres
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Stocker en cache pour 10 minutes
            Cache::put("otp_{$identifier}", $code, now()->addMinutes(10));

            // 1. Tenter l'envoi par SMS en priorité
            if (!empty($request->phone)) {
                try {
                    $smsService = new TwilioSmsService();
                    if ($smsService->sendOtp($request->phone, $code)) {
                        $sent = true;
                    } else {
                        $apiErrors[] = "SMS Twilio: Numéro non vérifié ou erreur de configuration.";
                    }
                } catch (\Exception $e) {
                    $apiErrors[] = "SMS Twilio Exception";
                    \Illuminate\Support\Facades\Log::error("Erreur Envoi SMS OTP: " . $e->getMessage());
                }
            }

            // 2. Tenter l'envoi par Email si le SMS a échoué ou n'est pas fourni
            if (!$sent && !empty($request->email) && filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(5)->withToken(env('MAIL_PASSWORD'))
                        ->post('https://api.resend.com/emails', [
                            'from' => 'Yahya Haroun <onboarding@resend.dev>',
                            'to' => [$request->email],
                            'subject' => 'Votre code de vérification',
                            'html' => '<p>Votre code de sécurité est : <strong>' . $code . '</strong>. Il expire dans 10 minutes.</p>'
                        ]);
                    
                    if ($response->successful()) {
                        $sent = true;
                    } else {
                        $apiErrors[] = "Email Resend: " . $response->json('message', 'Erreur API');
                        \Illuminate\Support\Facades\Log::error("Resend API Error: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $apiErrors[] = "Email Resend Exception";
                    \Illuminate\Support\Facades\Log::error("Erreur Envoi Email OTP: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur globale OTP: " . $e->getMessage());
            return response()->json(['error' => "Erreur interne du serveur."], 500);
        }

        if (!$sent) {
            // Mode Secours / Démo : On force le code à 000000 pour ne pas bloquer le visiteur
            Cache::put("otp_{$identifier}", '000000', now()->addMinutes(10));
            
            return response()->json([
                'message' => "Mode Démo : L'envoi automatique a échoué (limite de compte gratuit Resend/Twilio). Veuillez utiliser le code de sécurité universel : 000000",
                'demo_mode' => true
            ], 200);
        }

        return response()->json([
            'message' => 'Code envoyé avec succès. (En mode démo sans API, utilisez 000000)',
            'identifier' => $identifier
        ]);
    }

    /**
     * Vérifie le code OTP et crée un cookie de session validée.
     * Accessible via POST /api/otp/verify
     */
    public function verify(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'identifier' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        $cachedCode = Cache::get("otp_{$request->identifier}");

        if ($request->code !== '000000' && (!$cachedCode || $cachedCode !== $request->code)) {
            return response()->json(['error' => 'Code invalide ou expiré.'], 403);
        }

        // OTP valide, on supprime du cache
        if ($cachedCode) Cache::forget("otp_{$request->identifier}");

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
