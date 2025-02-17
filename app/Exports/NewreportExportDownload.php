<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class NewreportExportDownload implements FromView
{
    protected $progressreport;

    public function __construct($hasil)
    {
        $this->progressreport = $hasil;
    }

    public function view(): View
    {
        return view('newreports.exports.newreportdownload', [
            'progressreport' => $this->progressreport
        ]);
    }
}

