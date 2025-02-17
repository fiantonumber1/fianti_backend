<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class NewreportExport implements FromView
{
    protected $hasil;

    public function __construct($hasil)
    {
        $this->hasil = $hasil;
    }

    public function view(): View
    {
        return view('newreports.exports.newreport', [
            'hasil' => $this->hasil
        ]);
    }
}
