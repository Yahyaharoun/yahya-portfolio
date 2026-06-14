<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EnsureOtpValidated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // On vérifie le cookie ou le header Authorization Bearer
        $token = $request->cookie('otp_validated') ?? $request->bearerToken();

        if (!$token || !Cache::has("validated_session_{$token}")) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Validation OTP requise.',
                    'message' => 'Veuillez appeler /api/otp/send puis /api/otp/verify pour valider votre identité avant cette action.'
                ], 403);
            }

            // Fallback pour Inertia / Web sans modifier l'UI : On renvoie une 403 propre.
            // Le front-end pourra être mis à jour plus tard pour intercepter cette 403 et afficher le popup.
            abort(403, 'Validation OTP requise. Vous devez vérifier votre email ou numéro de téléphone.');
        }

        return $next($request);
    }
}
