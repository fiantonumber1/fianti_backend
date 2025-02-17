<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BomsImport implements ToCollection
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
        foreach ($collection as $key => $row) {
            // Check if both row[2] and row[3] exist
            $listkalimatterlarang = ["Kode Material", "Material / Komponen", "III", "DOCUMENT TO BE SUPPLIED"];
            if (isset($row[2]) && isset($row[3]) && !in_array($row[2], $listkalimatterlarang) && !in_array($row[3], $listkalimatterlarang)) {
                $kodeMaterial = $row[2]; 
                $material = $row[3];
                // Add material data to revisiData array
                $revisiData[] = [
                    'kodematerial' => $kodeMaterial,
                    'material' => $material,
                    'status' => "0"
                ];
            }
        }

        return $revisiData;
    }
}
