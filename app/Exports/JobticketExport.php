<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class JobticketExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return [
            'Proyek',
            'Unit',
            'No Dokumen',
            'Jenis Dokumen',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function map($row): array
    {
        return [
            $row['Proyek'],
            $row['Unit'],
            $row['No Dokumen'],
            $row['Jenis Dokumen'],
            $row['Status'],
            $row['Tanggal Dibuat'],
        ];
    }
}