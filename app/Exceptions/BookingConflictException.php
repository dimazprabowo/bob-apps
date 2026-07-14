<?php

namespace App\Exceptions;

use RuntimeException;

class BookingConflictException extends RuntimeException
{
    public static function room(): self
    {
        return new self('Ruangan sudah dibooking pada waktu tersebut. Silakan pilih waktu atau ruangan lain.');
    }

    public static function vehicle(): self
    {
        return new self('Kendaraan ini sudah dibooking pada rentang tanggal tersebut. Silakan pilih tanggal atau kendaraan lain.');
    }

    public static function zoom(): self
    {
        return new self('Sudah ada booking meeting pada waktu tersebut. Silakan pilih waktu lain.');
    }
}
