<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ColumnAImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        return $rows;
    }
}
