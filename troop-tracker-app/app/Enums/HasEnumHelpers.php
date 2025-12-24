<?php

declare(strict_types=1);

namespace App\Enums;

trait HasEnumHelpers
{
    /**
     * Return an associative array of enum case value => name.
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        $cases = self::cases();

        // Sort by case->name
        usort($cases, fn($a, $b) => strcmp($a->name, $b->name));

        $pairs = [];

        foreach ($cases as $case)
        {
            $pairs[$case->value] = $case->name;
        }

        return $pairs;
    }

    /**
     * Return an comma delimited string.
     *
     * @return string
     */
    public static function toValidator(): string
    {
        $cases = self::cases();

        // Sort by case->name
        usort($cases, fn($a, $b) => strcmp($a->name, $b->name));

        $values = [];

        foreach ($cases as $case)
        {
            $values[] = $case->value;
        }

        return implode(',', $values);
    }
}