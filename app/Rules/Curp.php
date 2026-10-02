<?php

namespace App\Rules;

use App\Models\State;
use Closure;
use DateTimeImmutable;
use Illuminate\Contracts\Validation\ValidationRule;

class Curp implements ValidationRule
{
    /**
     * Diccionario utilizado para calcular
     * el dígito verificador de la CURP.
     */
    private const CHECKSUM_DICTIONARY = '0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';

    /**
     * Valida la estructura e integridad de la CURP.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('La CURP no es válida.');
            return;
        }

        $curp = mb_strtoupper(
            (string) preg_replace(
                '/[\s-]+/u',
                '',
                trim($value)
            )
        );

        /*
         * Estructura:
         *
         *  1-4   Iniciales del nombre y apellidos
         *  5-10  Fecha de nacimiento AAMMDD
         *  11    Sexo H, M o X
         *  12-13 Entidad federativa de nacimiento
         *  14-16 Consonantes internas
         *  17    Diferenciador
         *  18    Dígito verificador
         */

        $pattern = '/\A'
            . '([A-ZÑ])'
            . '([AEIOUX])'
            . '([A-ZÑ])'
            . '([A-ZÑ])'
            . '(\d{2})'
            . '(0[1-9]|1[0-2])'
            . '(0[1-9]|[12]\d|3[01])'
            . '([HMX])'
            . '([A-Z]{2})'
            . '([B-DF-HJ-NP-TV-ZÑX])'
            . '([B-DF-HJ-NP-TV-ZÑX])'
            . '([B-DF-HJ-NP-TV-ZÑX])'
            . '([0-9A-Z])'
            . '(\d)'
            . '\z/u';

        if (preg_match($pattern, $curp, $matches) !== 1) {
            $fail(
                'La CURP debe tener 18 caracteres y una estructura válida.'
            );
            return;
        }

        $year = $matches[5];
        $month = $matches[6];
        $day = $matches[7];

        $entity = $matches[9];
        $differentiator = $matches[13];

        /*
         * Valida que la entidad federativa exista
         * en el catálogo almacenado en la base de datos.
         *
         * NE corresponde a personas nacidas
         * en el extranjero.
         */
        $stateExists = State::query()
            ->where('clave_curp', $entity)
            ->exists();

        if (! $stateExists && $entity !== 'NE') {
            $fail(
                'La entidad federativa contenida en la CURP no es válida.'
            );
            return;
        }

        /*
         * El carácter 17 permite determinar
         * el siglo aproximado de nacimiento:
         *
         * número -> 1900-1999
         * letra  -> 2000-2099
         */
        $fullYear = ctype_digit($differentiator)
            ? 1900 + (int) $year
            : 2000 + (int) $year;

        if (! $this->dateExists(
            $fullYear,
            (int) $month,
            (int) $day
        )) {
            $fail(
                'La fecha de nacimiento contenida en la CURP no es válida.'
            );
            return;
        }
        /*
         * Comprueba el dígito verificador
         * ubicado en la posición 18.
         */
        if (! $this->hasValidChecksum($curp)) {
            $fail(
                'El dígito verificador de la CURP no es válido.'
            );
        }
    }

    /**
     * Comprueba que la fecha exista realmente.
     */
    private function dateExists(int $year, int $month, int $day): bool
    {
        $date = DateTimeImmutable::createFromFormat(
            '!Y-n-j',
            "{$year}-{$month}-{$day}"
        );

        if ($date === false) {
            return false;
        }

        return (int) $date->format('Y') === $year
            && (int) $date->format('n') === $month
            && (int) $date->format('j') === $day;
    }

    /**
     * Calcula el dígito verificador de la CURP.
     */
    private function hasValidChecksum(string $curp): bool
    {
        $characters = preg_split(
            '//u',
            mb_substr($curp, 0, 17),
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        if ($characters === false) {
            return false;
        }

        $sum = 0;

        foreach ($characters as $index => $character) {
            $value = mb_strpos(
                self::CHECKSUM_DICTIONARY,
                $character
            );

            if ($value === false) {
                return false;
            }

            /*
             * Los pesos van de 18 hasta 2.
             */
            $weight = 18 - $index;

            $sum += $value * $weight;
        }

        $expectedDigit = (10 - ($sum % 10)) % 10;

        $actualDigit = (int) mb_substr(
            $curp,
            -1
        );

        return $expectedDigit === $actualDigit;
    }
}
