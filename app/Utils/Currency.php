<?php

declare(strict_types=1);

namespace App\Utils;

final class Currency
{
    private const int MINOR_UNITS_PER_MAJOR = 100;

    /**
     * Convert major currency units to minor units (multiply by 100, round to handle floating point precision)
     * Examples: $1.23 -> 123, €4.56 -> 456, ¥7.89 -> 789
     */
    public static function toMinorUnits(float $majorUnits): int
    {
        return (int) round($majorUnits * self::MINOR_UNITS_PER_MAJOR);
    }

    /**
     * Convert minor currency units to major units (divide by 100)
     * Examples: 123 -> $1.23, 456 -> €4.56, 789 -> ¥7.89
     */
    public static function toMajorUnits(int $minorUnits): float
    {
        return round($minorUnits / self::MINOR_UNITS_PER_MAJOR, 2);
    }

    /**
     * Add minor units (useful for adding cents/pence safely)
     */
    public static function addMinorUnits(float $majorUnits, int $minorUnitsToAdd): float
    {
        $currentMinorUnits = self::toMinorUnits($majorUnits);
        $newMinorUnits = $currentMinorUnits + $minorUnitsToAdd;

        return self::toMajorUnits($newMinorUnits);
    }
}
