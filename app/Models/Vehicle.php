<?php

namespace App\Models;

use App\Enums\VehicleCategory;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'plate_number',
        'category',
        'status',
        'contract_date',
        'contract_expiry',
        'contract_company',
        'tax_expiry',
        'stnk_expiry',
        'image',
        'description',
    ];

    protected $casts = [
        'category' => VehicleCategory::class,
        'status' => VehicleStatus::class,
        'contract_date' => 'date',
        'contract_expiry' => 'date',
        'tax_expiry' => 'date',
        'stnk_expiry' => 'date',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(VehicleBooking::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', VehicleStatus::Available);
    }

    public function scopeByCategory($query, VehicleCategory $category)
    {
        return $query->where('category', $category);
    }

    public function getTaxStatusAttribute(): array
    {
        if (!$this->tax_expiry) {
            return ['status' => 'none', 'label' => 'Tidak Ada Data', 'color' => 'gray'];
        }

        $daysLeft = Carbon::now()->startOfDay()->diffInDays($this->tax_expiry, false);

        if ($daysLeft < 0) {
            return ['status' => 'expired', 'label' => 'Pajak Habis', 'color' => 'red'];
        } elseif ($daysLeft <= 30) {
            return ['status' => 'warning', 'label' => "Sisa {$daysLeft} hari", 'color' => 'yellow'];
        }

        return ['status' => 'active', 'label' => 'Aktif', 'color' => 'green'];
    }

    public function getContractStatusAttribute(): array
    {
        if (!$this->contract_expiry) {
            return ['status' => 'none', 'label' => 'Tidak Ada Kontrak', 'color' => 'gray'];
        }

        $daysLeft = Carbon::now()->startOfDay()->diffInDays($this->contract_expiry, false);

        if ($daysLeft < 0) {
            return ['status' => 'expired', 'label' => 'Kontrak Habis', 'color' => 'red'];
        } elseif ($daysLeft <= 60) {
            return ['status' => 'warning', 'label' => "Sisa {$daysLeft} hari", 'color' => 'yellow'];
        }

        return ['status' => 'active', 'label' => 'Aktif', 'color' => 'green'];
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
        return $this->status === VehicleStatus::Available;
    }
}
