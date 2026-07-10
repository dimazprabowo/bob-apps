<?php

namespace App\Exports;

use App\Models\RoomBooking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RoomBookingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = RoomBooking::with(['room', 'user', 'approver']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('booking_code', 'like', "%{$this->search}%")
                  ->orWhere('purpose', 'like', "%{$this->search}%")
                  ->orWhere('guest_name', 'like', "%{$this->search}%")
                  ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$this->search}%"));
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
            'Ruangan',
            'Lokasi',
            'Peminjam',
            'Divisi',
            'No. HP',
            'Tanggal',
            'Jam Mulai',
            'Jam Selesai',
            'Tujuan',
            'Jumlah Peserta',
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
            $booking->room?->name ?? '-',
            $booking->room?->location ?? '-',
            $booking->booker_name,
            $booking->booker_divisi ?? '-',
            $booking->booker_phone ?? '-',
            $booking->booking_date->format('d/m/Y'),
            $booking->start_time->format('H:i'),
            $booking->end_time->format('H:i'),
            $booking->purpose,
            $booking->participants,
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
