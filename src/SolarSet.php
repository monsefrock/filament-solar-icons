<?php

namespace Monsefeledrisse\LaravelSolarIcons;

enum SolarSet: string
{
    case Outline = 'solar-outline';
    case Linear = 'solar-linear';
    case Bold = 'solar-bold';
    case BoldDuotone = 'solar-bold-duotone';
    case Broken = 'solar-broken';
    case LineDuotone = 'solar-line-duotone';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
