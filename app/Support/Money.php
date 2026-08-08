<?php

namespace App\Support;

use InvalidArgumentException;

final class Money
{
    public static function toMinor(string|int $amount): int
    {
        $value = trim((string) $amount);
        if (! preg_match('/^-?\d+(\.\d{1,2})?$/', $value)) {
            throw new InvalidArgumentException('Format uang tidak valid.');
        }$negative = str_starts_with($value, '-');
        $value = ltrim($value, '-');
        [$whole,$fraction] = array_pad(explode('.', $value, 2), 2, '');
        $minor = ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');

        return $negative ? -$minor : $minor;
    }

    public static function fromMinor(int $minor): string
    {
        $negative = $minor < 0;
        $minor = abs($minor);

        return ($negative ? '-' : '').intdiv($minor, 100).'.'.str_pad((string) ($minor % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function basisPoints(int $minor, int $basisPoints): int
    {
        return intdiv(($minor * $basisPoints) + 5000, 10000);
    }
}
