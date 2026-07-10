<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CheckTaxExpiryCommand extends Command
{
    protected $signature = 'bookings:check-tax-expiry';
    protected $description = 'Check vehicles with expiring tax/STNK and notify admins';

    public function handle(): int
    {
        $thresholdDays = (int) config('app.tax_expiry_threshold', 30);
        $thresholdDate = Carbon::now()->addDays($thresholdDays);

        $vehicles = Vehicle::where(function ($q) use ($thresholdDate) {
            $q->where('tax_expiry', '<=', $thresholdDate)
              ->orWhere('stnk_expiry', '<=', $thresholdDate);
        })->get();

        if ($vehicles->isEmpty()) {
            $this->info('No vehicles with expiring tax/STNK found.');
            return self::SUCCESS;
        }

        foreach ($vehicles as $vehicle) {
            $warnings = [];
            if ($vehicle->tax_expiry && $vehicle->tax_expiry <= $thresholdDate) {
                $warnings[] = "Pajak: {$vehicle->tax_expiry->format('d M Y')}";
            }
            if ($vehicle->stnk_expiry && $vehicle->stnk_expiry <= $thresholdDate) {
                $warnings[] = "STNK: {$vehicle->stnk_expiry->format('d M Y')}";
            }

            $this->warn("{$vehicle->name} ({$vehicle->plate_number}): " . implode(', ', $warnings));

            $adminIds = Role::where('name', 'admin')->first()?->users()->pluck('users.id')->toArray() ?? [];
            if (!empty($adminIds)) {
                NotificationService::sendToMany(
                    $adminIds,
                    'Peringatan Pajak/STNK',
                    "Kendaraan {$vehicle->name} ({$vehicle->plate_number}) akan/habis pajak/STNK: " . implode(', ', $warnings),
                    'warning',
                    null,
                    route('master-data.vehicles'),
                );
            }
        }

        $this->info("Notified {$vehicles->count()} vehicle(s) with expiring tax/STNK.");
        return self::SUCCESS;
    }
}
