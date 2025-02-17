<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HazardLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'hazard_ref',
        'proyek_type',
        'operating_mode',
        'system',
        'hazard',
        'hazard_cause',
        'accident',
        'IF',
        'IS',
        'resolution_status',
        'source',
        'haz_owner',
        'hazard_status',
        'date_updated',
        'RF',
        'RS',
        'RR',
        'IR',
        'verification_evidence_reference',
        'validation_evidence_reference',
        'comments',
        'due_date',
        'status',
        'hazard_unit'
    ];

    public function hazardlogfeedback()
    {
        return $this->hasMany(HazardLogFeedback::class, 'hazard_log_id');
    }

    public function reductionMeasures()
    {
        return $this->hasMany(HazardLogReductionMeasure::class);
    }

    public function detailonehazardLog()
    {
        $data = $this->getVerificatorData();
        $temporaryrule = "GUEST";
        $ramsvalidation = "Aktif";
        $unitpicvalidation = $data['unitpicvalidation'];
        $unitvalidation = $data['unitvalidation'];
        $ramscombinevalidation = $data['ramscombinevalidation'];

        $hazardLogopenedclosed = $data['hazardLogopenedclosed'];


        if (auth()->check()) {
            $temporaryrule = auth()->user()->rule;
        }

        // Default values
        $posisi1 = "on";
        $posisi2 = "off";
        $posisi3 = "off";

        // Determine which position should be "on"


        if ($ramsvalidation == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "on";
            $posisi3 = "off";
        }

        if ($unitvalidation == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "off";
            $posisi3 = "on";
        }

        if ($ramscombinevalidation == "Aktif") {
            $posisi1 = "on";
            $posisi2 = "on";
            $posisi3 = "on";
        }


        $parameter = [
            'temporaryrule' => $temporaryrule,
            'ramsvalidation' => $ramsvalidation,
            'posisi1' => $posisi1,
            'posisi2' => $posisi2,
            'posisi3' => $posisi3,
            'unitpicvalidation' => $unitpicvalidation,
            'unitvalidation' => $unitvalidation,
            'ramscombinevalidation' => $ramscombinevalidation,
            'hazardLogopenedclosed ' => $hazardLogopenedclosed
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
        $hazardLogs = self::with('hazardlogfeedback')->get();
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

        //loop hazardLog
        foreach ($hazardLogs as $hazardLog) {

            $parameter = $hazardLog->detailonehazardLog();
            $hazardLog->ramsvalidation = $parameter['ramsvalidation'];
            $hazardLog->unitpicvalidation = $parameter['unitpicvalidation'];
            $hazardLog->ramscombinevalidation = $parameter['ramscombinevalidation'];
            $hazardLog->unitvalidation = $parameter['unitvalidation'];
            $hazardLog->hazardLogopenedclosed = $parameter['hazardLogopenedclosed '];

            $hazardLog->posisi1 = $parameter['posisi1'];
            $hazardLog->posisi2 = $parameter['posisi2'];
            $hazardLog->posisi3 = $parameter['posisi3'];
            $hazardLog->unitsingkatan = $unitsingkatan;
            $hazardLog->projectpics = json_decode($hazardLog->hazard_unit);
        }

        return $hazardLogs;
    }

    public function getVerificatorData()
    {
        $feedbacks = $this->hazardlogfeedback;
        $hazardLogopenedclosed = "Terbuka";
        $hazard_unit = [];
        $unitpicvalidation = [];
        $unitvalidation = "Nonaktif";
        $ramscombinevalidation = "Nonaktif";

        // Membuat list yang berisi array keseluruhan $ramshazardLog_unit dengan status nonaktif
        if (!empty($this->hazard_unit)) {
            $hazard_unit = json_decode($this->hazard_unit);
            foreach ($hazard_unit as $picname) {
                $unitpicvalidation[$picname] = 'Nonaktif';
            }
        }



        // Menginisialisasi unit dengan status 'Belum dibaca'
        if (!empty($this->hazard_unit)) {
            $hazard_unit = json_decode($this->hazard_unit);
            foreach ($hazard_unit as $picname) {
                $unitpicvalidation[$picname] = 'Belum dibaca';
            }
        }

        // Memproses feedback untuk menentukan status unit
        foreach ($feedbacks as $ramshazardLogfeedback) {
            $picstate = $ramshazardLogfeedback->pic;
            if (strpos($picstate, 'Manager ') === 0) {
                $picstate = str_replace('Manager ', '', $picstate);
            }

            if (in_array($picstate, $hazard_unit)) {
                $conditionoffile = $ramshazardLogfeedback->conditionoffile;
                if ($conditionoffile == "approve") {
                    $unitpicvalidation[$picstate] = "Aktif";
                }
                $level = $ramshazardLogfeedback->level;
                if ($level == $picstate && $unitpicvalidation[$picstate] == 'Belum dibaca') {
                    $unitpicvalidation[$picstate] = 'Ongoing';
                }
            }

            if ($ramshazardLogfeedback->conditionoffile2 == "combine" && $ramshazardLogfeedback->conditionoffile == "approve") {
                $ramscombinevalidation = "Aktif";
            }
        }

        foreach ($this->reductionMeasures as $reductionMeasure) {
            $unit = $reductionMeasure->unit_name;
            // if($reductionMeasure->status=="reject"){
            //     $unitpicvalidation[$unit] = 'Aktif';
            // }
        }

        $activeValidations = array_filter($unitpicvalidation, function ($value) {
            return $value == "Aktif";
        });

        if (count($unitpicvalidation) == count($activeValidations)) {
            if (!empty($this->hazard_unit)) {
                $unitvalidation = "Aktif";
                if ($unitvalidation == "Aktif" && $ramscombinevalidation != "Aktif") {
                    $ramscombinevalidation = 'Belum dibaca';
                }
            }
        }

        return [
            'unitpicvalidation' => $unitpicvalidation,
            'unitvalidation' => $unitvalidation,
            'ramscombinevalidation' => $ramscombinevalidation,
            'hazardLogopenedclosed' => $hazardLogopenedclosed
        ];
    }

}
