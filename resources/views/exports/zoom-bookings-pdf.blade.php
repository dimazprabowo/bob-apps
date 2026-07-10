<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Booking Meeting Online</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #2563eb; margin: 0 0 4px; }
        .header p { font-size: 11px; color: #6b7280; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2563eb; color: #ffffff; padding: 8px 6px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-approved { background-color: #dcfce7; color: #166534; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }
        .badge-completed { background-color: #dbeafe; color: #1e40af; }
        .badge-cancelled { background-color: #f3f4f6; color: #374151; }
        .footer { text-align: right; margin-top: 15px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'BKI One Book Apps') }}</h1>
        <p>Laporan Booking Meeting Online &mdash; {{ isset($dateFrom) ? $dateFrom : '' }} s/d {{ isset($dateTo) ? $dateTo : '' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Topik</th>
                <th>Peminjam</th>
                <th>Tanggal</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Platform</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 0; @endphp
            @foreach($bookings as $booking)
                @php $no++; @endphp
                <tr>
                    <td>{{ $no }}</td>
                    <td>{{ $booking->booking_code }}</td>
                    <td>{{ $booking->topic }}</td>
                    <td>{{ $booking->booker_name }}</td>
                    <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                    <td>{{ $booking->start_time->format('H:i') }}</td>
                    <td>{{ $booking->end_time->format('H:i') }}</td>
                    <td>{{ $booking->platform }}</td>
                    <td><span class="badge badge-{{ $booking->status->value }}">{{ $booking->status->label() }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Dicetak pada {{ now()->format('d F Y, H:i') }}</div>
</body>
</html>
