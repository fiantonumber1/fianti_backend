<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProgressItemExport implements FromArray, WithHeadings, WithMapping
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
            'nodokumen',
            'namadokumen',
            'level',
            'drafter',
            'checker',
            'deadlinerelease',
            'documentkind',
            'realisasi',
            'status',
        ];
    }

    public function map($row): array
    {
        return [
            $row['nodokumen'],
            $row['namadokumen'],
            $row['level'],
            $row['drafter'],
            $row['checker'],
            $row['deadlinerelease'],
            $row['documentkind'],
            $row['realisasi'],
            $row['status'],
            
        ];
    }
}
