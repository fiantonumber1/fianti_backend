<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProgressreportsTreediagramImport implements ToCollection
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
        $penyimpananIndukSementara = [];

        foreach ($collection as $key => $row) {
            if ($key === 0) {
                continue; // Skip the header row
            }

            $nowGeneration = null;
            $parent = '';

            for ($i = 0; $i < 8; $i++) {
                if (!empty($row[$i])) {
                    if ($i === 0) {
                        $parent = "";
                        $nowGeneration = $row[0];
                        $penyimpananIndukSementara[strval(0)] = $nowGeneration;
                        $revisiData[$key] = [
                            'noindukdokumen' => $parent,
                            'nodokumen' => "$nowGeneration",
                        ];
                        break;
                    } else {
                        $parentIndex = strval($i - 1);
                        $parent = isset($penyimpananIndukSementara[$parentIndex]) ? $penyimpananIndukSementara[$parentIndex] : '';
                        $nowGeneration = $row[$i];
                        $penyimpananIndukSementara[strval($i)] = $nowGeneration;
                        $revisiData[$key] = [
                            'noindukdokumen' => $parent,
                            'nodokumen' => $nowGeneration,
                        ];
                        break;
                    }
                }
            }
        }

        return $revisiData;
    }
}
