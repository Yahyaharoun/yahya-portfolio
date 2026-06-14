<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhoneRegex implements ValidationRule
{
    // Règles strictes demandées par indicatif
    protected array $phoneRules = [
        '+237' => '/^\+237[23689]\d{8}$/', // Cameroun: +237 suivi de 9 chiffres (commençant par 2,3,6,8,9)
        '+33'  => '/^\+33[1-9]\d{8}$/',   // France: +33 suivi de 9 chiffres
        '+225' => '/^\+225\d{10}$/',      // Côte d'Ivoire: +225 suivi de 10 chiffres
        '+221' => '/^\+221[78]\d{8}$/',   // Sénégal: +221 suivi de 9 chiffres
        '+241' => '/^\+241[0-9]{7,8}$/',  // Gabon: +241 suivi de 7 ou 8 chiffres
        '+242' => '/^\+242[0-9]{9}$/',    // Congo: +242 suivi de 9 chiffres
        '+212' => '/^\+212[5-7]\d{8}$/',  // Maroc: +212 suivi de 9 chiffres
        '+216' => '/^\+216[0-9]{8}$/',    // Tunisie: +216 suivi de 8 chiffres
        '+213' => '/^\+213[5-7]\d{8}$/',  // Algérie: +213 suivi de 9 chiffres
        '+234' => '/^\+234[789]\d{9}$/',  // Nigeria: +234 suivi de 10 chiffres
        '+27'  => '/^\+27[0-9]{9}$/',     // Afrique du Sud: +27 suivi de 9 chiffres
        '+254' => '/^\+254[0-9]{9}$/',    // Kenya: +254 suivi de 9 chiffres
        '+32'  => '/^\+32[0-9]{8,9}$/',   // Belgique: +32 suivi de 8 ou 9 chiffres
        '+41'  => '/^\+41[0-9]{9}$/',     // Suisse: +41 suivi de 9 chiffres
        '+1'   => '/^\+1[2-9]\d{9}$/',    // US/Canada: +1 suivi de 10 chiffres
        '+44'  => '/^\+44[7-9]\d{9}$/',   // UK: +44 suivi de 10 chiffres
        '+49'  => '/^\+49[0-9]{10,11}$/', // Allemagne: +49 suivi de 10 ou 11 chiffres
        '+86'  => '/^\+86[1]\d{10}$/',    // Chine: +86 suivi de 11 chiffres
        '+91'  => '/^\+91[6-9]\d{9}$/',   // Inde: +91 suivi de 10 chiffres
        '+971' => '/^\+9715[0-9]{8}$/',   // UAE: +971 suivi de 9 chiffres
        '+65'  => '/^\+65[89]\d{7}$/',    // Singapour: +65 suivi de 8 chiffres
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = preg_replace('/\s+/', '', $value); // Nettoyer les espaces
        
        $matched = false;
        foreach ($this->phoneRules as $prefix => $regex) {
            if (str_starts_with($value, $prefix)) {
                if (!preg_match($regex, $value)) {
                    $fail("Le format du numéro pour l'indicatif {$prefix} est invalide.");
                }
                $matched = true;
                break;
            }
        }
        
        if (!$matched) {
            // Validation de base pour les autres pays non définis strictement
            if (!preg_match('/^\+[1-9]\d{6,14}$/', $value)) {
                $fail("Le format du numéro de téléphone est invalide. N'oubliez pas l'indicatif (ex: +237).");
            }
        }
    }
}
