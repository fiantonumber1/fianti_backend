<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DokumensExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $documents;
    protected $listdatadocuments;

    public function __construct($documents, $listdatadocuments)
    {
        $this->documents = $documents;
        $this->listdatadocuments = $listdatadocuments;
    }

    public function collection()
    {
        return $this->documents->map(function ($document) {
            $listdatadocuments = $this->listdatadocuments[$document->id];
            $unitpicvalidation = $listdatadocuments['unitpicvalidation'];

            $replaceValues = function ($value) {
                if ($value === 'Aktif') {
                    return 'Disetujui Manager';
                } elseif ($value === 'Ongoing') {
                    return 'Menunggu Persetujuan Manager';
                }
                return $value;
            };

            return [
                'Nama Memo' => $document->documentname,
                'Nomor Memo' => $document->documentnumber,
                'Project' => $document->project_type,

                'Product Engineering' => $replaceValues($unitpicvalidation['Product Engineering'] ?? "Tidak Terlibat"),
                'Mechanical Engineering System' => $replaceValues($unitpicvalidation['Mechanical Engineering System'] ?? "Tidak Terlibat"),
                'Electrical Engineering System' => $replaceValues($unitpicvalidation['Electrical Engineering System'] ?? "Tidak Terlibat"),
                'Quality Engineering' => $replaceValues($unitpicvalidation['Quality Engineering'] ?? "Tidak Terlibat"),
                'RAMS' => $replaceValues($unitpicvalidation['RAMS'] ?? "Tidak Terlibat"),

                'Desain Mekanik & Interior' => $replaceValues($unitpicvalidation['Desain Mekanik & Interior'] ?? "Tidak Terlibat"),
                'Desain Bogie & Wagon' => $replaceValues($unitpicvalidation['Desain Bogie & Wagon'] ?? "Tidak Terlibat"),
                'Desain Carbody' => $replaceValues($unitpicvalidation['Desain Carbody'] ?? "Tidak Terlibat"),
                'Desain Elektrik' => $replaceValues($unitpicvalidation['Desain Elektrik'] ?? "Tidak Terlibat"),

                'Preparation & Support' => $replaceValues($unitpicvalidation['Preparation & Support'] ?? "Tidak Terlibat"),
                'Welding Technology' => $replaceValues($unitpicvalidation['Welding Technology'] ?? "Tidak Terlibat"),
                'Shop Drawing' => $replaceValues($unitpicvalidation['Shop Drawing'] ?? "Tidak Terlibat"),
                'Teknologi Proses' => $replaceValues($unitpicvalidation['Teknologi Proses'] ?? "Tidak Terlibat"),

                'Status Dokumen' => $document->documentstatus,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Memo',
            'Nomor Memo',
            'Project',

            'Product Engineering',
            'Mechanical Engineering System',
            'Electrical Engineering System',
            'Quality Engineering',
            'RAMS',

            'Desain Mekanik & Interior',
            'Desain Bogie & Wagon',
            'Desain Carbody',
            'Desain Elektrik',

            'Preparation & Support',
            'Welding Technology',
            'Shop Drawing',
            'Teknologi Proses',

            'Status Dokumen',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                for ($row = 2; $row <= $highestRow; $row++) {
                    for ($col = 'A'; $col <= $highestColumn; $col++) {
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        $styleArray = [];

                        switch ($cellValue) {
                            case 'Tidak Terlibat':
                                $styleArray = [
                                    'fill' => [
                                        'fillType' => Fill::FILL_SOLID,
                                        'color' => ['argb' => '000000'], // Black background
                                    ],
                                    'font' => [
                                        'color' => ['argb' => 'FFFFFF'] // White font for readability
                                    ]
                                ];
                                break;

                            case 'Disetujui Manager':
                                $styleArray = [
                                    'fill' => [
                                        'fillType' => Fill::FILL_SOLID,
                                        'color' => ['argb' => '00FF00'], // Green background
                                    ],
                                ];
                                break;

                            case 'Sudah dibaca':
                                $styleArray = [
                                    'fill' => [
                                        'fillType' => Fill::FILL_SOLID,
                                        'color' => ['argb' => '0000FF'], // Blue background
                                    ],
                                ];
                                break;

                            case 'Belum dibaca':
                                $styleArray = [
                                    'fill' => [
                                        'fillType' => Fill::FILL_SOLID,
                                        'color' => ['argb' => 'FFFF00'], // Yellow background
                                    ],
                                ];
                                break;

                            case 'Menunggu Persetujuan Manager':
                                $styleArray = [
                                    'fill' => [
                                        'fillType' => Fill::FILL_SOLID,
                                        'color' => ['argb' => 'FFA500'], // Orange background
                                    ],
                                ];
                                break;
                        }

                        if (!empty($styleArray)) {
                            $sheet->getStyle($col . $row)->applyFromArray($styleArray);
                        }
                    }
                }
            }
        ];
    }
}
