<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class RawprogressreportsImport implements ToCollection
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

            $proyek_type = $row[1] ?? "";
            $nodokumen = $row[2] ?? "";
            $namadokumen = $row[3] ?? "";
            $realisasi = $this->transformDate($row[9] ?? "");
            $drafter = $row[10] ?? "";
            $checker = $row[11] ?? "";
            $unit = $row[13] ?? "";
            
            
            $revisiData[$nodokumen] = [
                'proyek_type' => $proyek_type,
                'nodokumen' => $nodokumen,
                'namadokumen' => $namadokumen,
                'realisasi' => $realisasi,
                'drafter' => $drafter,
                'checker' => $checker,
                'unit' => $unit,
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
