<?php

namespace App\Livewire\Reports;

use App\Exports\ZoomBookingsExport;
use App\Livewire\Traits\HasNotification;
use App\Services\ZoomBookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class ZoomReport extends Component
{
    use AuthorizesRequests, HasNotification;

    public $dateFrom = '';
    public $dateTo = '';
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
        return (new ZoomBookingsExport(null, null, $this->dateFrom ?: null, $this->dateTo ?: null))
            ->download('laporan-zoom-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function exportPdf(ZoomBookingService $service)
    {
        $this->authorize('reports_export_pdf');
        $bookings = $service->getBookingsByDateRange($this->dateFrom ?: null, $this->dateTo ?: null);
        $pdf = Pdf::loadView('exports.zoom-bookings-pdf', [
            'bookings' => $bookings,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ]);
        $pdf->setPaper('a4', 'landscape');
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'laporan-zoom-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    public function render(ZoomBookingService $service)
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

        return view('livewire.reports.zoom-report', [
            'bookings' => $bookings,
            'stats' => $stats,
            'dailyStats' => $dailyStats,
        ]);
    }
}
