<?php
namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetJobticketExport implements WithMultipleSheets
{
    protected $drafterDocLists;
    protected $checkerDocLists;
    protected $startDate;
    protected $endDate;

    protected $name;

    public function __construct($drafterDocLists, $checkerDocLists, $startDate, $endDate, $name)
    {
        $this->drafterDocLists = $drafterDocLists;
        $this->checkerDocLists = $checkerDocLists;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->name = $name;
    }

    public function sheets(): array
    {
        return [
            'Drafter' => new JobticketSheetExport($this->drafterDocLists, $this->startDate, $this->endDate, 'Drafter', $this->name),
            'Checker' => new JobticketSheetExport($this->checkerDocLists, $this->startDate, $this->endDate, 'Checker', $this->name),
        ];
    }
}

