<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class RoomBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'room_id',
        'user_id',
        'approved_by',
        'guest_name',
        'guest_phone',
        'guest_divisi',
        'guest_email',
        'guest_ip',
        'booking_date',
        'start_time',
        'end_time',
        'purpose',
        'participants',
        'status',
        'notes',
        'approved_at',
    ];

    protected $casts = [
        'status' => BookingStatus::class,
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [BookingStatus::Pending, BookingStatus::Approved]);
    }

    public function scopePending($query)
    {
        return $query->where('status', BookingStatus::Pending);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', BookingStatus::Approved);
    }

    public function getBookerNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? '-';
    }

    public function getBookerPhoneAttribute(): ?string
    {
        return $this->user?->phone ?? $this->guest_phone;
    }

    public function getBookerDivisiAttribute(): ?string
    {
        return $this->guest_divisi ?? ($this->user?->position ?? null);
    }

    public function getBookerEmailAttribute(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    public function isGuest(): bool
    {
        return is_null($this->user_id);
    }

    public function getTimeRangeAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public static function generateBookingCode(): string
    {
        return 'RB-' . now()->format('YmdHis');
    }

    public function getRouteKey()
    {
        return Crypt::encryptString($this->getKey());
    }

    public function resolveRouteBinding($value, $field = null)
    {
        try {
            $decryptedId = Crypt::decryptString($value);
            return $this->where($this->getRouteKeyName(), $decryptedId)->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
