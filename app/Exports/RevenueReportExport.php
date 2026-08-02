<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RevenueReportExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private array $data) {}

    public function title(): string
    {
        return 'Revenue Report';
    }

    public function headings(): array
    {
        return ['Metric', 'Value (GHS)'];
    }

    public function collection(): Collection
    {
        return collect([
            ['Period From',         $this->data['from']],
            ['Period To',           $this->data['to']],
            ['Total Revenue',       number_format($this->data['totalRevenue'], 2)],
            ['Room Revenue',        number_format($this->data['roomRevenue'], 2)],
            ['Services Revenue',    number_format($this->data['servicesRevenue'], 2)],
            ['Tax Collected',       number_format($this->data['taxCollected'], 2)],
            ['Cash Payments',       number_format($this->data['byMethod']['cash'] ?? 0, 2)],
            ['Card Payments',       number_format($this->data['byMethod']['card'] ?? 0, 2)],
            ['Mobile Money',        number_format($this->data['byMethod']['mobile_money'] ?? 0, 2)],
            ['Payment Count',       $this->data['paymentCount']],
        ]);
    }
}
