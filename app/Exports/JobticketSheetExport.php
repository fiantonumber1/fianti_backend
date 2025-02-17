<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JobticketSheetExport implements FromView
{
    protected $docLists;
    protected $startDate;
    protected $endDate;
    protected $kind;

    protected $name;

    public function __construct($docLists, $startDate, $endDate, $kind, $name)
    {
        $this->docLists = $docLists;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->kind = $kind;
        $this->name = $name;
    }

    public function view(): View
    {
        return view('jobticket.export', [
            'docLists' => $this->docLists,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'kind' => $this->kind,
            'name' => $this->name,
        ]);
    }
}

