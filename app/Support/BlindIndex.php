<?php

namespace App\Support;

use Illuminate\Support\Str;
use LogicException;

final class BlindIndex
{
    public static function email(string $email): string
    {
        return self::make(self::normalizeEmail($email));
    }

    public static function rfc(string $rfc): string
    {
        return self::make(self::normalizeRfc($rfc));
    }

    public static function curp(string $curp): string
    {
        return self::make(self::normalizeCurp($curp));
    }

    public static function socialSecurityNumber(string $number): string
    {
        return self::make(self::normalizeSocialSecurityNumber($number));
    }

    public static function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    public static function normalizeRfc(string $rfc): string
    {
        return Str::upper((string) preg_replace('/[\s-]+/u', '', trim($rfc)));
    }

    public static function normalizeCurp(string $curp): string
    {
        return Str::upper((string) preg_replace('/[\s-]+/u', '', trim($curp)));
    }

    public static function normalizeSocialSecurityNumber(string $number): string
    {
        return (string) preg_replace('/[\s-]+/', '', trim($number));
    }

    private static function make(string $value): string
    {
        $configuredKey = config('data_protection.blind_index_key');

        if (! is_string($configuredKey) || $configuredKey === '') {
            throw new LogicException('BLIND_INDEX_KEY o APP_KEY debe estar configurada.');
        }

        $key = Str::startsWith($configuredKey, 'base64:')
            ? base64_decode(Str::after($configuredKey, 'base64:'), true)
            : $configuredKey;

        if ($key === false || $key === '') {
            throw new LogicException('La llave para índices HMAC no es válida.');
        }

        return hash_hmac('sha256', $value, $key);
    }
}
