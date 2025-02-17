<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RamsDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentname',
        'documentnumber',
        'proyek_type',
        'ramsdocument_unit',
    ];

    /**
     * Get the files associated with the document.
     */
    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }

    /**
     * Get the feedbacks associated with the document.
     */
    public function feedbacks()
    {
        return $this->hasMany(RamsDocumentFeedback::class, 'rams_document_id');
    }

    /**
     * Method to fetch detail of one document.
     * Adjusted to return a single array instead of an array inside an array.
     */
    public function detailonedocument()
    {
        $data = $this->getVerificatorData();
        $temporaryrule = "GUEST";
        $ramsvalidation = "Aktif";
        $unitpicvalidation = $data['unitpicvalidation'];
        $unitvalidation = $data['unitvalidation'];
        $ramscombinevalidation = $data['ramscombinevalidation'];
        $ramscombinesendvalidation = $data['ramscombinesendvalidation'];

        $smunitpicvalidation = $data['smunitpicvalidation'];
        $smunitvalidation = $data['smunitvalidation'];
        $ramsfinalisasivalidation = $data['ramsfinalisasivalidation'];
        $documentopenedclosed = $data['documentopenedclosed'];


        if (auth()->check()) {
            $temporaryrule = auth()->user()->rule;
        }

        // Default values
        $posisi1 = "on";
        $posisi2 = "off";
        $posisi3 = "off";
        $posisi4 = "off";
        $posisi5 = "off";

        // Determine which position should be "on"


        if ($ramsvalidation == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "on";
            $posisi3 = "off";
            $posisi4 = "off";
            $posisi5 = "off";
        }


        if ($unitvalidation == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "off";
            $posisi3 = "on";
            $posisi4 = "off";
            $posisi5 = "off";
        }

        if ($ramscombinesendvalidation == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "off";
            $posisi3 = "off";
            $posisi4 = "on";
            $posisi5 = "off";
        }

        if ($smunitvalidation == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "off";
            $posisi3 = "off";
            $posisi4 = "off";
            $posisi5 = "on";
        }



        if ($documentopenedclosed == "Tertutup") {
            $posisi1 = "on";
            $posisi2 = "on";
            $posisi3 = "on";
            $posisi4 = "on";
            $posisi5 = "on";
        }


        $parameter = [
            'temporaryrule' => $temporaryrule,
            'ramsvalidation' => $ramsvalidation,
            'posisi1' => $posisi1,
            'posisi2' => $posisi2,
            'posisi3' => $posisi3,
            'posisi4' => $posisi4,
            'posisi5' => $posisi5,
            'unitpicvalidation' => $unitpicvalidation,
            'unitvalidation' => $unitvalidation,
            'ramscombinevalidation' => $ramscombinevalidation,
            'ramscombinesendvalidation' => $ramscombinesendvalidation,
            'smunitpicvalidation' => $smunitpicvalidation,
            'smunitvalidation' => $smunitvalidation,
            'ramsfinalisasivalidation' => $ramsfinalisasivalidation,
            'documentopenedclosed ' => $documentopenedclosed
        ];

        return $parameter;
    }
    public static function singkatanUnit($namaUnit)
    {
        $singkatan = "";
        $kata = explode(" ", $namaUnit);
        foreach ($kata as $k) {
            $singkatan .= substr($k, 0, 1);
        }
        return $singkatan;
    }
    public static function infoplus()
    {
        $documents = self::with('feedbacks')->get();
        $allunitunderpe = ['Product Engineering', 'Mechanical Engineering System', 'Electrical Engineering System', 'Quality Engineering', 'Desain Mekanik & Interior', 'Desain Bogie & Wagon', 'Desain Carbody', 'Desain Elektrik', 'Preparation & Support', 'Welding Technology', 'Shop Drawing', 'Teknologi Proses'];
        $allunitundersm = ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"];

        //singkatan
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = self::singkatanUnit($unit);
        }
        foreach ($allunitundersm as $unit) {
            $unitsingkatan[$unit] = self::singkatanUnit($unit);
        }

        //loop document
        foreach ($documents as $document) {

            $parameter = $document->detailonedocument();
            $document->ramsvalidation = $parameter['ramsvalidation'];
            $document->unitpicvalidation = $parameter['unitpicvalidation'];
            $document->ramscombinevalidation = $parameter['ramscombinevalidation'];
            $document->ramscombinesendvalidation = $parameter['ramscombinesendvalidation'];
            $document->smunitpicvalidation = $parameter['smunitpicvalidation'];
            $document->unitvalidation = $parameter['unitvalidation'];
            $document->ramsfinalisasivalidation = $parameter['ramsfinalisasivalidation'];
            $document->documentopenedclosed = $parameter['documentopenedclosed '];

            $document->posisi1 = $parameter['posisi1'];
            $document->posisi2 = $parameter['posisi2'];
            $document->posisi3 = $parameter['posisi3'];
            $document->posisi4 = $parameter['posisi4'];
            $document->posisi5 = $parameter['posisi5'];
            $document->unitsingkatan = $unitsingkatan;
            $document->projectpics = json_decode($document->ramsdocument_unit);
        }

        return $documents;
    }

    public function getVerificatorData()
    {
        $feedbacks = $this->feedbacks;
        $documentopenedclosed = "Terbuka";
        $unitpicvalidation = [];
        $unitvalidation = "Nonaktif"; // Tambah variabel baru
        $ramscombinevalidation = "Nonaktif";
        $ramscombinesendvalidation = "Nonaktif";
        $smunitpicvalidation = [];
        $smunitvalidation = "Nonaktif";
        $ramsfinalisasivalidation = "Nonaktif";
        $ramsUnit = [];


        // Membuat list yang berisi array keseluruhan $ramsdocument_unit dengan status nonaktif
        if (!empty($this->ramsdocument_unit)) {
            $ramsUnit = json_decode($this->ramsdocument_unit);
            foreach ($ramsUnit as $picname) {
                $unitpicvalidation[$picname] = 'Belum dibaca';
            }
        }
        $allunitundersm = [];
        foreach ($feedbacks as $ramsdocumentfeedback) {
            $conditionoffile = $ramsdocumentfeedback->conditionoffile;
            if ($conditionoffile === 'filesend') {
                $allunitundersm[] = $ramsdocumentfeedback->level;
            }
        }
        foreach ($allunitundersm as $picname) {
            $smunitpicvalidation[$picname] = 'Nonaktif';
        }


        foreach ($feedbacks as $ramsdocumentfeedback) {
            $picstate = $ramsdocumentfeedback->pic;

            // Menghapus kata 'Manager ' jika ada di awal string
            if (strpos($picstate, 'Manager ') === 0) {
                $picstate = str_replace('Manager ', '', $picstate);
                if (in_array($picstate, $ramsUnit)) {
                    $conditionoffile = $ramsdocumentfeedback->conditionoffile;
                    if ($conditionoffile == "approve") {
                        $unitpicvalidation[$picstate] = "Aktif";
                    }
                }
            }

            if (strpos($picstate, 'Senior Manager ') === 0) {
                if (in_array($picstate, $allunitundersm)) {
                    $conditionoffile = $ramsdocumentfeedback->conditionoffile;
                    if ($conditionoffile == "approve") {
                        $smunitpicvalidation[$picstate] = "Aktif";
                    }
                }
            }


            if ($ramsdocumentfeedback->conditionoffile == 'filesend') {
                $ramscombinesendvalidation = "Aktif";
            }

            if ($ramsdocumentfeedback->conditionoffile2 == "combine" && $ramsdocumentfeedback->conditionoffile == "approve") {
                $ramscombinevalidation = "Aktif";
                foreach ($allunitundersm as $picname) {
                    $smunitpicvalidation[$picname] = 'Ongoing';
                }
            }

            if ($ramsdocumentfeedback->conditionoffile2 == "finalisasi" && $ramsdocumentfeedback->conditionoffile == "approve") {
                $ramsfinalisasivalidation = "Aktif";

            }

            if (in_array($picstate, $allunitundersm)) {
                $conditionoffile = $ramsdocumentfeedback->conditionoffile;
                if ($conditionoffile == "approve") {
                    $smunitpicvalidation[$picstate] = "Aktif";
                }
            }

        }

        $activeValidations = array_filter($unitpicvalidation, function ($value) {
            return $value == "Aktif";
        });
        if (count($unitpicvalidation) == count($activeValidations)) {
            if (!empty($this->ramsdocument_unit)) {
                $unitvalidation = "Aktif";
                if ($unitvalidation == "Aktif" && $ramscombinevalidation != "Aktif") { // Perbaiki penugasan nilai di sini
                    $ramscombinevalidation = 'Belum dibaca';
                }
            }
        }


        if (count($smunitpicvalidation) != 0) {
            $activeSMValidations = array_filter($smunitpicvalidation, function ($value) {
                return $value == "Aktif";
            });

            if (count($smunitpicvalidation) == count($activeSMValidations)) {
                $smunitvalidation = "Aktif";
                if ($smunitvalidation == "Aktif" && $ramsfinalisasivalidation != "Aktif") { // Perbaiki penugasan nilai di sini
                    $ramsfinalisasivalidation = 'Belum dibaca';
                }
            }
        }


        if ($ramsfinalisasivalidation == "Aktif") {
            $documentopenedclosed = "Tertutup";
        }

        return [
            'unitpicvalidation' => $unitpicvalidation,
            'unitvalidation' => $unitvalidation,
            'ramscombinevalidation' => $ramscombinevalidation,
            'smunitpicvalidation' => $smunitpicvalidation,
            'smunitvalidation' => $smunitvalidation,
            'ramsfinalisasivalidation' => $ramsfinalisasivalidation,
            'ramscombinesendvalidation' => $ramscombinesendvalidation,
            'documentopenedclosed' => $documentopenedclosed

        ];
    }



}
