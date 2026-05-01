<?php

namespace Illimi\Health\Enums;

/**
 * Incident Severity Enum
 */
enum IncidentSeverityEnum: string
{
    case Minor = 'minor';
    case Moderate = 'moderate';
    case Severe = 'severe';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Minor => 'Minor',
            self::Moderate => 'Moderate',
            self::Severe => 'Severe',
            self::Critical => 'Critical',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
