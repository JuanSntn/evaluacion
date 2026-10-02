<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Rfc implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('El RFC debe tener 12 o 13 caracteres y una estructura válida.');

            return;
        }

        $matches = [];
        $hasValidStructure = preg_match(
            '/\A[A-ZÑ&]{3,4}(\d{6})[A-Z0-9]{3}\z/u',
            $value,
            $matches,
        ) === 1;

        if (! $hasValidStructure || ! $this->hasValidDate($matches[1])) {
            $fail('El RFC debe tener 12 o 13 caracteres y una estructura válida.');
        }
    }

    private function hasValidDate(string $date): bool
    {
        $year = (int) substr($date, 0, 2);
        $month = (int) substr($date, 2, 2);
        $day = (int) substr($date, 4, 2);

        return checkdate($month, $day, 1900 + $year)
            || checkdate($month, $day, 2000 + $year);
    }
}
