<?php

namespace App\Livewire\Reports;

use App\Exports\VehicleBookingsExport;
use App\Livewire\Traits\HasNotification;
use App\Services\VehicleBookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class FleetReport extends Component
{
    use AuthorizesRequests, HasNotification;

    public $dateFrom = '';
    public $dateTo = '';
    public $reportType = 'daily';
    public bool $filterChanged = false;

    public function mount()
    {
        $this->authorize('reports_view');
    }

    public function updatingDateFrom() { $this->filterChanged = true; }
    public function updatingDateTo() { $this->filterChanged = true; }

    public function resetFilters()
    {
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->dispatch('filters-reset');
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function exportExcel()
    {
        $this->authorize('reports_export_excel');
        return (new VehicleBookingsExport(null, null, $this->dateFrom ?: null, $this->dateTo ?: null))
            ->download('laporan-armada-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf(VehicleBookingService $service)
    {
        $this->authorize('reports_export_pdf');
        $bookings = $service->getBookingsByDateRange($this->dateFrom ?: null, $this->dateTo ?: null);
        $pdf = Pdf::loadView('exports.vehicle-bookings-pdf', [
            'bookings' => $bookings,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ]);
        $pdf->setPaper('a4', 'landscape');
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'laporan-armada-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(VehicleBookingService $service)
    {
        $bookings = $service->getBookingsByDateRange($this->dateFrom ?: null, $this->dateTo ?: null);
        $stats = $service->getDashboardStats();

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$bookings->count()} data booking.");
            $this->filterChanged = false;
        }

        $dailyStats = $bookings->groupBy(fn ($b) => $b->booking_date->format('Y-m-d'))
            ->map(fn ($group, $date) => [
                'date' => $date,
                'total' => $group->count(),
                'approved' => $group->where('status.value', 'approved')->count(),
                'pending' => $group->where('status.value', 'pending')->count(),
                'rejected' => $group->where('status.value', 'rejected')->count(),
                'completed' => $group->where('status.value', 'completed')->count(),
            ])->values();

        return view('livewire.reports.fleet-report', [
            'bookings' => $bookings,
            'stats' => $stats,
            'dailyStats' => $dailyStats,
        ]);
    }
}
