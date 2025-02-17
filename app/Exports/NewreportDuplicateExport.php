<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class NewreportDuplicateExport implements FromView
{
    protected $informasi;

    public function __construct(array $informasi)
    {
        $this->informasi = $informasi;
    }

    public function view(): View
    {
        return view('newreports.exports.newreport_duplicate', [
            'informasi' => $this->informasi
        ]);
    }
}
