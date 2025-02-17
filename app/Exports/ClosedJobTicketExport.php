<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class ClosedJobTicketExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    private $datas;

    public function __construct($datas)
    {
        $this->datas = $datas;
    }

    public function collection()
    {
        return new Collection($this->datas);
    }

    public function headings(): array
    {
        return [
            'Document Name',
            'Rev',
            'Document Number',
            'Updated At',
        ];
    }
}