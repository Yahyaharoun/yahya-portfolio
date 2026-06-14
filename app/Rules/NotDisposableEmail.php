<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotDisposableEmail implements ValidationRule
{
    protected array $disposableDomains = [
        'yopmail.com', 'tempmail.com', 'trashmail.com', '10minutemail.com', 'mailinator.com', 'guerrillamail.com', 'throwawaymail.com', 'sharklasers.com', 'temp-mail.org', 'yopmail.fr'
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $domain = substr(strrchr($value, "@"), 1);
        if (in_array(strtolower($domain), $this->disposableDomains)) {
            $fail('Les adresses e-mail temporaires ou jetables ne sont pas autorisées.');
        }
    }
}
