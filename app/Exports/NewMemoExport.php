<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class NewMemoExport implements WithMultipleSheets
{
    protected $newmemos;

    public function __construct(Collection $newmemos)
    {
        $this->newmemos = $newmemos;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Kelompokkan data berdasarkan proyek
        $projects = $this->newmemos->groupBy(function ($memo) {
            return $memo->projectType->title ?? 'Tanpa Proyek';
        });

        foreach ($projects as $projectName => $projectMemos) {
            // Kelompokkan lagi berdasarkan status
            $statusGroups = $projectMemos->groupBy('documentstatus');

            foreach ($statusGroups as $status => $statusMemos) {
                $statusLabel = $status === 'Terbuka' ? 'Terbuka' : 'Tertutup';
                $sheetName = "{$projectName} - {$statusLabel}";
                $sheets[] = new NewMemoPerProjectSheet($sheetName, $statusMemos);
            }
        }

        return $sheets;
    }
}
