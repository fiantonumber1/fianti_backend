<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class NewMemoPerProjectSheet implements FromView, WithTitle
{
    protected $sheetName;
    protected $newmemos;

    public function __construct($sheetName, $newmemos)
    {
        $this->sheetName = $sheetName;
        $this->newmemos = $newmemos;
    }

    public function view(): View
    {
        return view('exports.newmemo_per_project', [
            'projectName' => $this->sheetName,
            'newmemos' => $this->newmemos
        ]);
    }

    /**
     * Set the title of the sheet.
     *
     * @return string
     */
    public function title(): string
    {
        // Pastikan panjang judul tidak melebihi 31 karakter (batas Excel)
        return substr($this->sheetName, 0, 31);
    }
}
