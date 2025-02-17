<?php

namespace App\Exports;

use App\Models\Newbom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NewbomExport implements FromCollection, WithHeadings
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Mengambil data untuk diexport.
     */
    public function collection()
    {
        $newbom = Newbom::with('newbomkomats')->findOrFail($this->id);

        return $newbom->newbomkomats->map(function ($item, $index) use ($newbom) {
            return [
                'No' => $index + 1,
                'No BOM' => $newbom->BOMnumber ?? 'Unknown',
                'Material' => $item->material,
                'Kode Material' => $item->kodematerial,
                'Spesifikasi' => '', // Sesuaikan dengan kolom spesifikasi jika ada
            ];
        });
    }

    /**
     * Menentukan heading kolom.
     */
    public function headings(): array
    {
        return ['No', 'No BOM', 'Material', 'Kode Material', 'Spesifikasi'];
    }
}
