<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'capacity',
        'facilities',
        'image',
        'status',
        'description',
    ];

    protected $casts = [
        'facilities' => 'array',
        'status' => RoomStatus::class,
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(RoomBooking::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', RoomStatus::Available);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return asset('storage/' . $this->image);
    }

    public function isAvailable(): bool
    {
        return $this->status === RoomStatus::Available;
    }

    public function getFacilitiesListAttribute(): string
    {
        if (!$this->facilities) {
            return '-';
        }

        return implode(', ', $this->facilities);
    }
}
