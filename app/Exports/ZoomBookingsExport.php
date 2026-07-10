<?php

namespace App\Exports;

use App\Models\ZoomBooking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ZoomBookingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected ?string $search;
    protected ?string $status;
    protected ?string $dateFrom;
    protected ?string $dateTo;

    public function __construct(?string $search = null, ?string $status = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $this->search = $search;
        $this->status = $status;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function query()
    {
        $query = ZoomBooking::with(['user', 'approver']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('booking_code', 'like', "%{$this->search}%")
                  ->orWhere('topic', 'like', "%{$this->search}%")
                  ->orWhere('guest_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->dateFrom) {
            $query->where('booking_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('booking_date', '<=', $this->dateTo);
        }

        return $query->orderBy('booking_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Booking',
            'Topik',
            'Peminjam',
            'Divisi',
            'No. HP',
            'Tanggal',
            'Jam Mulai',
            'Jam Selesai',
            'Platform',
            'Meeting Link',
            'Status',
            'Approved By',
            'Tanggal Approved',
            'Catatan',
        ];
    }

    public function map($booking): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $booking->booking_code,
            $booking->topic,
            $booking->booker_name,
            $booking->booker_divisi ?? '-',
            $booking->booker_phone ?? '-',
            $booking->booking_date->format('d/m/Y'),
            $booking->start_time->format('H:i'),
            $booking->end_time->format('H:i'),
            $booking->platform,
            $booking->meeting_link ?? '-',
            $booking->status->label(),
            $booking->approver?->name ?? '-',
            $booking->approved_at?->format('d/m/Y H:i') ?? '-',
            $booking->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
            ],
        ];
    }
}
