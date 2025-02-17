<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProgressreportsImport implements ToCollection
{
     /**
     * Import data from Excel.
     *
     * @param Collection $collection
     * @return array
     */
    public function collection(Collection $collection)
    {
        $revisiData = [];
        $listkalimatterlarang = ["No Dokumen", "Nama Drawing/Dokumen"];

        foreach ($collection as $key => $row) {
            if ($key < 1) {
                continue;
            }

            // Ensure row has sufficient columns
            if ($row[0] == '' || $row[1] == '' ) {
                continue;
            }

            // Check if the row contains unwanted headers
            //if (in_array($row[1], $listkalimatterlarang) || in_array($row[2], $listkalimatterlarang)) {
            //    continue;
            //}

            $nodokumen = $row[0] ?? "";
            $namadokumen = $row[1] ?? "";
            $level = $row[2] ?? "";
            $drafter = $row[3] ?? "";
            $checker = $row[4] ?? "";
            $deadlinerelease = $this->transformDate($row[5] ?? "");
            $documentkind = $row[6] ?? "";
            $realisasi = $this->transformDate($row[7] ?? "");
            $status = $row[8] ?? "";

            // Add material data to revisiData array
            $revisiData[$nodokumen] = [
                'nodokumen' => $nodokumen,
                'namadokumen' => $namadokumen,
                'level' => $level,
                'drafter' => $drafter,
                'checker' => $checker,
                'deadlinerelease' => $deadlinerelease,
                'documentkind' => $documentkind,
                'realisasi' => $realisasi,
                'status' => $status,
            ];
        }

        return $revisiData;
    }
    /**
     * Transform Excel date serial number to Y-m-d format.
     *
     * @param mixed $value
     * @return string
     */
    private function transformDate($value)
    {
        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $date->format('d-m-Y');
        }

        return $value;
    }
}
