<?php

namespace App\Enums;

enum VehicleCategory: string
{
    case OperationalDaily = 'operasional_harian';
    case Project = 'project';
    case OperationalStructural = 'operasional_struktural';

    public function label(): string
    {
        return match ($this) {
            self::OperationalDaily => 'Operasional Harian',
            self::Project => 'Project',
            self::OperationalStructural => 'Operasional Struktural',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OperationalDaily => 'sky',
            self::Project => 'amber',
            self::OperationalStructural => 'emerald',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }
}
