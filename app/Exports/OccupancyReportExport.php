<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class OccupancyReportExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private Collection $data) {}

    public function title(): string
    {
        return 'Occupancy Report';
    }

    public function headings(): array
    {
        return ['Date', 'Rooms Occupied', 'Rooms Available', 'Total Rooms', 'Occupancy Rate (%)'];
    }

    public function collection(): Collection
    {
        return $this->data->map(fn($row) => [
            $row['date'],
            $row['occupied'],
            $row['available'],
            $row['total'],
            $row['occupancy_rate'],
        ]);
    }
}
