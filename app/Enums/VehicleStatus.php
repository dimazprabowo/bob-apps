<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Available = 'tersedia';
    case InUse = 'dipakai';
    case Maintenance = 'maintenance';
    case Unavailable = 'tidak_tersedia';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::InUse => 'Dipakai',
            self::Maintenance => 'Maintenance',
            self::Unavailable => 'Tidak Tersedia',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'green',
            self::InUse => 'blue',
            self::Maintenance => 'yellow',
            self::Unavailable => 'red',
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
