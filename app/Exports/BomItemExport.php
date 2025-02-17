<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BomItemExport implements FromArray, WithHeadings, WithMapping
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Kode Material',
            'Material',
            'Spesifikasi',
            'Status',
            'Detail Bom',
        ];
    }

    public function map($row): array
    {
        return [
            $row['kodematerial'],
            $row['material'],
            $row['spesifikasi'],
            $row['status'],
            $row['detailbom'],
        ];
    }
}
