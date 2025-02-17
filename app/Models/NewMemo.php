<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewMemo extends Model
{
    protected $fillable = ['documentname', 'documentnumber', 'proyek_type', 'category', 'documentstatus', 'memokind', 'memoorigin', 'asliordummy', 'operator', 'project_pic', 'proyek_type_id'];

    // fungsi relasi

    public function feedbacks()
    {
        return $this->hasMany(NewMemoFeedback::class);
    }

    public function komats()
    {
        return $this->hasMany(NewMemoKomat::class);
    }

    public function timelines()
    {
        return $this->hasMany(NewMemoTimeline::class);
    }

    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }

    public function notifsystem()
    {
        return $this->morphMany(Notification::class, 'notifmessage');
    }


    public function notifications()
    {
        return $this->hasMany(Notification::class, 'idunit');
    }


    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }

    // fungsi kreasi

    public function UnitVerfied($picname, $userinformation, $projectpics)
    {
        return in_array($picname, $projectpics) &&
            in_array($userinformation->conditionoffile, ["Approved", "Terkirim"]);
    }

    public function detailonedocument()
    {
        $operator = $this->operator;
        $unitstepverificator = [];
        $temporaryrule = "GUEST";
        if (isset(auth()->user()->rule)) {
            $temporaryrule = auth()->user()->rule;
        }
        $userinformations = $this->feedbacks;


        $notif = $this->timelines;
        foreach ($notif as $n) {
            // Decode notifarray into a PHP array
            $notifArray = $n->infostatus;

            // Check if type is "share"
            if (isset($notifArray) && $notifArray === $operator . '_share_read') {
                // Get created_at time and store it in the variable
                $firstShareTime = $n->created_at;
                $this->operatorshare_array = [
                    'time' => $firstShareTime,
                    'author' => "PENULIS",
                ];
                break;
            }
        }


        $timelines = collect($this->timelines); // Menggunakan collect untuk $timelines
        $MTPRsend = "Aktif"; // Tambah variabel baru
        $operatorsignature = "Nonaktif"; // Tambah variabel baru
        $operatorshare = "Nonaktif";
        $unitpicvalidation = [];
        $selfunitvalidation = "Nonaktif"; // Tambah variabel baru
        $unitvalidation = "Nonaktif"; // Tambah variabel baru
        $operatorcombinevalidation = "Nonaktif"; // Tambah variabel baru
        $manageroperatorvalidation = "Tidak Terlibat"; // Tambah variabel baru
        $seniormanagervalidation = "Nonaktif"; // Tambah variabel baru
        $MTPRvalidation = "Nonaktif"; // Tambah variabel baru
        $SMname = "Belum ditentukan";
        $tutuppaksa = "Nonaktif"; // Tambah variabel baru    
        $projectpics = [];
        $antarkondisi = "";
        $totalSteps = 0;
        $this->verification_status = false;



        $this->unitpicvalidation_array = [];
        if (!empty($this->project_pic)) {
            $operatorshare = "Aktif";
            $projectpics = json_decode($this->project_pic);
            foreach ($projectpics as $picname) {
                $unitpicvalidation[$picname] = "Nonaktif";
                $unitpicvalidation_array = $this->unitpicvalidation_array;
                $unitpicvalidation_array[$picname]['author'] = "Belum ada PIC";
                $unitpicvalidation_array[$picname]['time'] = null;
                $this->unitpicvalidation_array = $unitpicvalidation_array;

            }
        }


        foreach ($userinformations as $userinformation) {
            $picname = $userinformation->pic;
            $levelname = $userinformation->level;
            if ($userinformation->level == "signature") {
                $operatorshare_array = $this->operatorshare_array;
                $operatorshare_array['author'] = $userinformation->author;
                $this->operatorshare_array = $operatorshare_array;
            }


            // Update verification status based on conditions
            if ($this->documentstatus == "Tertutup" && $userinformation->level == "selesai") {
                $this->verification_status = true;
            } elseif ($this->documentstatus == "Tertutup" && $userinformation->conditionoffile2 == "tutupterpaksa") {
                $this->verification_status = true;
            }


            if ($levelname == "pembukadokumen") {
                if ($picname == "MTPR") {
                    $this->withMTPR = "Yes";
                    $this->MTPRsend_array = [
                        'author' => $userinformation->author,
                        'time' => $userinformation->created_at,
                    ];
                } else {
                    $this->withMTPR = "No";
                }
            }

            //operatorsignature
            if (in_array($picname, [$operator]) && in_array($levelname, ["signature"])) {
                $operatorsignature = "Aktif";
            }
            if (in_array($picname, ["MTPR"]) && in_array($levelname, ["selesai"])) {
                $MTPRvalidation = "Aktif";
                $this->MTPRvalidation_array = [
                    'author' => $userinformation->author,
                    'time' => $userinformation->created_at,
                ];
            }


        }


        if ($operator == "Product Engineering") {

            foreach ($userinformations as $userinformation) {
                $picname = $userinformation->pic;
                $levelname = $userinformation->level;





                if ($userinformation->conditionoffile2 == "feedback") {

                    //selfunitvalidation awal
                    if (
                        ($picname == $temporaryrule || $levelname == $temporaryrule) &&
                        in_array($userinformation->conditionoffile, ["Approved", "Approved by Manager"])
                    ) {
                        $selfunitvalidation = "Aktif";
                    }

                    // Unit PIC Validation and Unit Validation
                    if (!empty($projectpics)) {
                        $operatorshare = "Aktif";

                        // Check for "Aktif" status
                        if (
                            in_array($picname, $projectpics) &&
                            in_array($userinformation->conditionoffile, ["Approved", "Approved by Manager"])
                        ) {
                            $unitpicvalidation[$picname] = "Aktif";
                            $unitpicvalidation_array = $this->unitpicvalidation_array;
                            $unitpicvalidation_array[$picname]['author'] = $userinformation->author;
                            $unitpicvalidation_array[$picname]['time'] = $userinformation->created_at;
                            $this->unitpicvalidation_array = $unitpicvalidation_array;
                        }

                        // Check for "Ongoing" status
                        if (
                            in_array($picname, $projectpics) &&
                            ($userinformation->conditionoffile2 ?? '') == "feedback"
                        ) {
                            if ($unitpicvalidation[$picname] != "Aktif") {
                                $unitpicvalidation[$picname] = "Ongoing";
                            }

                        }
                    }
                }


                //operatorcombinevalidation
                if ($picname == $operator) {
                    $nilaiinformasi = $userinformation->conditionoffile2;
                    if ($nilaiinformasi == "combine") {
                        if ($operatorcombinevalidation == "Nonaktif") {
                            $operatorcombinevalidation = "Ongoing";
                        }
                    }
                }

                if (in_array($picname, [$operator]) && in_array($levelname, ["Manager " . $operator, "Senior Manager Desain", "Senior Manager Teknologi Produksi"])) {
                    $operatorcombinevalidation = "Aktif";
                    $this->operatorcombinevalidation_array = [
                        'author' => $userinformation->author,
                        'time' => $userinformation->created_at,
                    ];
                }

                //seniormanagervalidation
                if (in_array($levelname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"])) {
                    $seniormanagervalidation = 'Belum dibaca';
                }
                if (in_array($picname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"]) && in_array($levelname, ["MTPR"])) {
                    $seniormanagervalidation = "Aktif";
                    $this->seniormanagervalidation_array = [
                        'author' => $userinformation->author,
                        'time' => $userinformation->created_at,
                    ];
                }


                //OPERATORACTIVATION
                if (in_array($levelname, ["Manager " . $operator])) {
                    $manageroperatorvalidation = "Terlibat";
                    if ($manageroperatorvalidation == "Terlibat") {
                        if ($operator == "Product Engineering") {
                            $SMname = "Senior Manager Engineering";
                        } elseif ($operator == "Desain Elektrik" || $operator == "Desain Carbody" || $operator == "Desain Bogie & Wagon" || $operator == "Desain Mekanik & Interior") {
                            $SMname = "Senior Manager Desain";
                        } elseif ($operator == "Teknologi Proses" || $operator == "Shop Drawing" || $operator == "Welding Technology" || $operator == "Preparation & Support") {
                            $SMname = "Senior Manager Teknologi Produksi";
                        }

                    }
                } elseif (in_array($levelname, ["Senior Manager Desain"])) {
                    $SMname = "Senior Manager Desain";
                } elseif (in_array($levelname, ["Senior Manager Teknologi Produksi"])) {
                    $SMname = "Senior Manager Teknologi Produksi";
                }

                if ($picname == "Manager " . $operator && $levelname == $SMname) {
                    $antarkondisi = "Aktif";
                    $this->manageroperatorvalidation_array = [
                        'author' => $userinformation->author,
                        'time' => $userinformation->created_at,
                    ];
                }

            }

            $activeValidations = array_filter($unitpicvalidation, function ($value) {
                return $value == "Aktif";
            });

            if (count($unitpicvalidation) == count($activeValidations)) {
                if (!empty($this->project_pic)) {
                    $unitvalidation = "Aktif";
                    if ($unitvalidation == "Aktif") { // Perbaiki penugasan nilai di sini
                        if ($operatorcombinevalidation == 'Nonaktif') {
                            $nama_divisi = $operator;
                            $timelineExist = $timelines->where('infostatus', $nama_divisi . '_combine' . '_read')
                                ->isNotEmpty();
                            if ($timelineExist) {
                                $operatorcombinevalidation = 'Sudah dibaca';
                            } else {
                                $operatorcombinevalidation = 'Belum dibaca';
                            }
                        }
                    }
                }
            }

            $totalSteps = 5; // Jumlah total langkah yang harus diselesaikan
            $completedSteps = 0; // Inisialisasi jumlah langkah yang sudah selesai

            if ($MTPRsend == 'Aktif') {
                $completedSteps++;
                if ($operatorshare == 'Nonaktif') {
                    $operatorshare = 'Belum dibaca';

                    $nama_divisi = $this->operator;

                    $timelineExist = $timelines->where('infostatus', $nama_divisi . '_share' . '_read')
                        ->isNotEmpty();

                    if (($timelineExist)) {
                        $operatorshare = 'Ongoing';
                    }
                }
            }
            if ($operatorshare == 'Aktif') {
                $completedSteps++;
                if (isset($projectpics)) {
                    if (!empty($this->project_pic)) {
                        foreach ($projectpics as $picname) {
                            if ($unitpicvalidation[$picname] == "Nonaktif") {
                                $unitpicvalidation[$picname] = "Belum dibaca";
                                $timelineExist1 = $timelines->where('infostatus', $picname . '_unit' . '_read')
                                    ->isNotEmpty();
                                if (($timelineExist1)) {
                                    $unitpicvalidation[$picname] = 'Sudah dibaca';
                                }
                            }
                        }
                    }
                }
            }

            if (isset($projectpics)) {
                for ($u = 0; $u < count($projectpics); $u++) {
                    $totalSteps++;
                    if ($unitpicvalidation[$projectpics[$u]] == 'Aktif') {
                        $completedSteps++;
                    }
                }
            }

            if ($operatorcombinevalidation == 'Aktif') {
                $completedSteps++;
                if ($manageroperatorvalidation == "Tidak Terlibat") {
                    if ($seniormanagervalidation == 'Nonaktif') {
                        $seniormanagervalidation = 'Belum dibaca';
                        $nama_divisis = ["Senior Manager Teknologi Produksi", "Senior Manager Engineering", "Senior Manager Desain",];
                        for ($k = 0; $k < count($nama_divisis); $k++) {
                            $timelineExist3 = $timelines->where('infostatus', $nama_divisis[$k] . '_seniorvalid' . '_read')
                                ->isNotEmpty();
                            if (($timelineExist3)) {
                                $seniormanagervalidation = "Ongoing";
                                break;
                            }
                        }
                    }
                } elseif ($manageroperatorvalidation == "Terlibat") {
                    $manageroperatorvalidation = 'Belum dibaca';
                    $timelineExist2 = $timelines->where('infostatus', "Manager " . $operator . '_unit' . '_read')
                        ->isNotEmpty();
                    if (($timelineExist2)) {
                        $manageroperatorvalidation = 'Sudah dibaca';
                        if ($antarkondisi == "Aktif") {
                            $manageroperatorvalidation = "Aktif";
                        }

                    }
                    if ($manageroperatorvalidation == 'Aktif') {
                        if ($seniormanagervalidation == 'Nonaktif') {
                            $seniormanagervalidation = 'Belum dibaca';
                            $nama_divisis = ["Senior Manager Teknologi Produksi", "Senior Manager Engineering", "Senior Manager Desain",];
                            for ($k = 0; $k < count($nama_divisis); $k++) {
                                $timelineExist3 = $timelines->where('infostatus', $nama_divisis[$k] . '_seniorvalid' . '_read')
                                    ->isNotEmpty();
                                if (($timelineExist3)) {
                                    $seniormanagervalidation = "Ongoing";
                                    break;
                                }
                            }
                        }
                    }
                }


            }
            if ($seniormanagervalidation == 'Aktif') {
                $completedSteps++;
                if ($MTPRvalidation == "Nonaktif") {
                    $MTPRvalidation = 'Belum dibaca';
                    $nama_divisi = "MTPR";
                    $timelineExist123 = $timelines->where('infostatus', $nama_divisi . '_finish' . '_read')
                        ->isNotEmpty();
                    if (($timelineExist123)) {
                        $MTPRvalidation = 'Ongoing';
                    }
                }

            }

            if ($MTPRvalidation == 'Aktif') {
                $completedSteps++;
            }


        } else {
            if ($operator == "Product Engineering") {
                $SMname = "Senior Manager Engineering";
            } elseif ($operator == "Desain Elektrik" || $operator == "Desain Carbody" || $operator == "Desain Bogie & Wagon" || $operator == "Desain Mekanik & Interior") {
                $SMname = "Senior Manager Desain";
            } elseif ($operator == "Teknologi Proses" || $operator == "Shop Drawing" || $operator == "Welding Technology" || $operator == "Preparation & Support") {
                $SMname = "Senior Manager Teknologi Produksi";
            }

            $unitstepverificator = [];
            $currentOngoingUnit = null; // Track the currently ongoing unit
            $rankCounter = 0; // Initialize the rank counter starting from 0

            // Function to check if a unit is approved


            // Initialize all units to "Access"
            foreach ($unitpicvalidation as $key => $value) {
                $unitstepverificator[$key] = ["status" => "Access", 'rank' => null];
            }

            // unitpicvalidation
            foreach ($userinformations as $userinformation) {

                $picname = $userinformation->pic;
                $levelname = $userinformation->level;

                //operatorsignature
                if (in_array($picname, [$operator]) && in_array($levelname, ["signature"])) {
                    $operatorsignature = "Aktif";
                }

                if ($userinformation->conditionoffile2 == "feedback") {


                    // Determine if this unit can enter the $unitstepverificator
                    if ($currentOngoingUnit === null) {
                        // Set the unit to "Ongoing"
                        $unitpicvalidation[$picname] = "Ongoing";
                        $unitstepverificator[$picname]["status"] = "Access"; // This unit has started working
                        $currentOngoingUnit = $picname; // Track the current ongoing unit
                        foreach ($unitstepverificator as $key => $value) {
                            if ($key != $picname) {
                                $unitstepverificator[$key]["status"] = "Not Access";
                            }

                        }
                    }

                    // Check if the current unit can proceed to become "Aktif"
                    if ($currentOngoingUnit === $picname && $this->UnitVerfied($picname, $userinformation, $projectpics)) {
                        $unitpicvalidation[$picname] = "Aktif";
                        $unitpicvalidation_array = $this->unitpicvalidation_array;
                        $unitpicvalidation_array[$picname]['author'] = $userinformation->author;
                        $unitpicvalidation_array[$picname]['time'] = $userinformation->created_at;
                        $this->unitpicvalidation_array = $unitpicvalidation_array;
                        $currentOngoingUnit = null; // Reset ongoing unit to allow the next unit to start
                        $unitstepverificator[$picname]["rank"] = $rankCounter++;
                        $unitstepverificator[$picname]["status"] = "Not Access";
                        // Reset all units to "Not Access"
                        foreach ($unitstepverificator as $key => $value) {
                            if ($unitpicvalidation[$key] != "Aktif") {
                                $unitstepverificator[$key]["status"] = "Access";
                            }

                        }
                    }
                }

                // seniormanagervalidation
                if (in_array($levelname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"])) {
                    $this->lastunitsendsm = 'Send';
                    if ($seniormanagervalidation == 'Nonaktif') {
                        $seniormanagervalidation = 'Belum dibaca';
                        $nama_divisis = ["Senior Manager Teknologi Produksi", "Senior Manager Engineering", "Senior Manager Desain",];
                        for ($k = 0; $k < count($nama_divisis); $k++) {
                            $timelineExist3 = $timelines->where('infostatus', $nama_divisis[$k] . '_seniorvalid' . '_read')
                                ->isNotEmpty();
                            if (($timelineExist3)) {
                                $seniormanagervalidation = "Ongoing";
                                break;
                            }
                        }
                    }
                }
                if (in_array($picname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"]) && in_array($levelname, ["MTPR"])) {
                    $seniormanagervalidation = "Aktif";
                    $this->seniormanagervalidation_array = [
                        'author' => $userinformation->author,
                        'time' => $userinformation->created_at,
                    ];
                }

                if ($operatorsignature == "Aktif") {

                    // unitpicvalidation
                    if (isset($projectpics)) {
                        if (!empty($this->project_pic)) {
                            foreach ($projectpics as $picname) {
                                if ($unitpicvalidation[$picname] == "Nonaktif") {
                                    $unitpicvalidation[$picname] = "Belum dibaca";
                                    $timelineExist1 = $timelines->where('infostatus', $picname . '_unit' . '_read')
                                        ->isNotEmpty();
                                    if (($timelineExist1)) {
                                        $unitpicvalidation[$picname] = 'Sudah dibaca';
                                    }
                                }
                            }
                        }
                    }
                }

            }

            // unitvalidation
            $activeValidations = array_filter($unitpicvalidation, function ($value) {
                return $value == "Aktif";
            });
            if (count($unitpicvalidation) == count($activeValidations)) {
                if (!empty($this->project_pic)) {
                    $unitvalidation = "Aktif";
                    $operatorcombinevalidation = "Aktif";
                    if ($unitvalidation == "Aktif") { // Perbaiki penugasan nilai di sini
                        if ($operatorcombinevalidation == 'Nonaktif') {
                            $nama_divisi = $operator;
                            $timelineExist = $timelines->where('infostatus', $nama_divisi . '_combine' . '_read')
                                ->isNotEmpty();
                            if ($timelineExist) {
                                $operatorcombinevalidation = 'Sudah dibaca';
                            } else {
                                $operatorcombinevalidation = 'Belum dibaca';
                            }
                        }
                    }
                }
            }

            // menentukan rank dan unitlaststep
            if ($this->project_pic !== null) {
                // Decode $this->project_pic dari JSON jika perlu
                $decodedProjectPic = json_decode($this->project_pic, true); // true untuk array asosiatif

                // Pastikan decodedProjectPic adalah array sebelum diurutkan
                if (is_array($decodedProjectPic)) {
                    // Urutkan $decodedProjectPic berdasarkan rank di $unitstepverificator
                    usort($decodedProjectPic, function ($a, $b) use ($unitstepverificator) {
                        $rankA = $unitstepverificator[$a]['rank'] ?? PHP_INT_MAX; // Default ke PHP_INT_MAX jika tidak ada rank
                        $rankB = $unitstepverificator[$b]['rank'] ?? PHP_INT_MAX; // Default ke PHP_INT_MAX jika tidak ada rank

                        return $rankA - $rankB;
                    });

                    // Set the last unit (with the highest rank) to unitlaststep
                    $this->unitlaststep = end($decodedProjectPic); // Ambil elemen terakhir setelah pengurutan

                    // Encode kembali menjadi JSON
                    $this->project_pic = json_encode($decodedProjectPic);
                } else {
                    // Handle error jika decodedProjectPic bukan array
                    // Misalnya, set project_pic ke array kosong atau log kesalahan
                    $this->project_pic = json_encode([]);
                    $this->unitlaststep = null; // Tidak ada unit terakhir jika array tidak valid
                }
            } else {
                // Jika project_pic adalah null, bisa di-set ke array kosong
                $this->project_pic = json_encode([]);
                $this->unitlaststep = null; // Tidak ada unit terakhir jika project_pic null
            }

            if ($MTPRsend == "Aktif") {
                if ($operatorshare == 'Nonaktif') {
                    $operatorshare = 'Belum dibaca';
                }
            }




        }

        if ($totalSteps == 0) {
            $positionPercentage = 0;
        } else {
            $positionPercentage = intval(($completedSteps / $totalSteps) * 100); // Mengonversi ke integer
        }

        if ($this->documentstatus == "Tertutup") {
            if ($MTPRvalidation != "Aktif") {
                $PEcombineworkstatus = "Status belum didefenisikan";
            }
            // 'documentshared' memiliki nilai
            $positionPercentage = 100;
            $MTPRsend = "Aktif"; // Tambah variabel baru
            $operatorshare = "Aktif"; // Tambah variabel baru
            if ($manageroperatorvalidation = "Terlibat") {
                $manageroperatorvalidation = "Aktif";
            }
            $operatorcombinevalidation = "Aktif"; // Tambah variabel baru
            $seniormanagervalidation = "Aktif"; // Tambah variabel baru
            $MTPRvalidation = "Aktif"; // Tambah variabel baru
            $selfunitvalidation = "Aktif"; // Tambah variabel baru
            $unitvalidation = "Aktif"; // Tambah variabel baru
            $operatorsignature = "Aktif"; // Tambah variabel baru    
            $tutuppaksa = "Aktif"; // Tambah variabel baru    
            if (isset($projectpics)) {
                if (!empty($this->project_pic)) {
                    foreach ($projectpics as $picname) {
                        $unitpicvalidation[$picname] = "Aktif";

                    }
                }
            }
        }

        $posisi1 = "on";
        $posisi2 = "off";
        $posisi3 = "off";

        if ($operatorshare == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "on";
            $posisi3 = "off";
        }

        if ($unitvalidation == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "off";
            $posisi3 = "on";
        }

        $this->MTPRsend = $MTPRsend;
        $this->operatorsignature = $operatorsignature;
        $this->operatorshare = $operatorshare;
        $this->unitpicvalidation = $unitpicvalidation;
        $this->unitvalidation = $unitvalidation;
        $this->operatorcombinevalidation = $operatorcombinevalidation;
        $this->selfunitvalidation = $selfunitvalidation;
        $this->seniormanagervalidation = $seniormanagervalidation;
        $this->MTPRvalidation = $MTPRvalidation;
        $this->manageroperatorvalidation = $manageroperatorvalidation;
        $this->posisi1 = $posisi1;
        $this->posisi2 = $posisi2;
        $this->posisi3 = $posisi3;
        $this->positionPercentage = $positionPercentage;
        $this->SMname = $SMname;
        $this->unitstepverificator = $unitstepverificator;
        $this->tutuppaksa = $tutuppaksa;



        // Return the current object
        return $this;


    }

    public function leadtimeunit()
    {
        // Project lead time
        $notif = $this->timelines;
        $operator = $this->operator;
        $userinformations = $this->feedbacks;

        // Initialize variable to store the first notification time with type "share"
        $firstShareTime = null;

        foreach ($notif as $n) {
            // Decode notifarray into a PHP array
            $notifArray = $n->infostatus;

            // Check if type is "share"
            if (isset($notifArray) && $notifArray === $operator . '_share_read') {
                // Get created_at time and store it in the variable
                $firstShareTime = $n->created_at;
                break; // Exit loop after finding the first occurrence
            }
        }

        $unitpicvalidation = []; // Initialize the validation array
        if (!empty($this->project_pic)) {
            $projectpics = json_decode($this->project_pic);
            foreach ($projectpics as $picname) {
                $unitpicvalidation[$picname] = null; // Set initial status to null if no lead time is available
            }
        }



        $currentOngoingUnit = null; // Initialize current ongoing unit
        $rankCounter = 1; // Initialize rank counter
        $unitstepverificator = []; // Initialize step verificator array

        if ($operator == "Product Engineering") {
            foreach ($userinformations as $userinformation) {
                $picname = $userinformation->pic;

                if ($userinformation->conditionoffile2 == "feedback") {
                    if (!empty($projectpics)) {
                        // Check for "Aktif" status
                        if (
                            in_array($picname, $projectpics) &&
                            in_array($userinformation->conditionoffile, ["Approved", "Approved by Manager"])
                        ) {
                            // Calculate time difference only if firstShareTime is set
                            if ($firstShareTime) {
                                $timeDifferenceInSeconds = strtotime($userinformation->created_at) - strtotime($firstShareTime);
                                $unitpicvalidation[$picname] = number_format($timeDifferenceInSeconds / 3600, 2); // Convert seconds to hours and format
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($userinformations as $userinformation) {
                $picname = $userinformation->pic;
                $levelname = $userinformation->level;

                // Check operator signature
                if (in_array($picname, [$operator]) && in_array($levelname, ["signature"])) {
                    $operatorsignature = "Aktif";
                }

                if ($userinformation->conditionoffile2 == "feedback") {
                    // Determine if this unit can enter the $unitstepverificator
                    if ($currentOngoingUnit === null) {
                        // Set the unit to "Ongoing"
                        $unitpicvalidation[$picname] = "Ongoing";
                        $unitstepverificator[$picname]["status"] = "Access"; // This unit has started working
                        $currentOngoingUnit = $picname; // Track the current ongoing unit
                        foreach ($unitstepverificator as $key => $value) {
                            if ($key != $picname) {
                                $unitstepverificator[$key]["status"] = "Not Access";
                            }
                        }
                    }

                    // Check if the current unit can proceed to become "Aktif"
                    if ($currentOngoingUnit === $picname && $this->UnitVerfied($picname, $userinformation, $projectpics)) {
                        // Calculate time difference only if firstShareTime is set
                        if ($firstShareTime) {
                            $timeDifferenceInSeconds = strtotime($userinformation->created_at) - strtotime($firstShareTime);
                            $unitpicvalidation[$picname] = number_format($timeDifferenceInSeconds / 3600, 2); // Convert seconds to hours and format
                        }
                        $currentOngoingUnit = null; // Reset ongoing unit to allow the next unit to start
                        $unitstepverificator[$picname]["rank"] = $rankCounter++;
                        $unitstepverificator[$picname]["status"] = "Not Access";

                        // Reset all units to "Not Access"
                        foreach ($unitstepverificator as $key => $value) {
                            if ($unitpicvalidation[$key] != "Aktif") {
                                $unitstepverificator[$key]["status"] = "Access";
                            }
                        }
                    }
                }
            }
        }

        return $unitpicvalidation; // Return the validation array
    }


    public static function leadtimeperunit()
    {
        // Retrieve all unit names as an array
        $units = Unit::all()->pluck('name')->toArray();

        // Eager load all necessary relationships for Newmemo
        $newMemos = self::with(['feedbacks', 'timelines'])->get();

        // Initialize array to separate memos by unit
        $memoByUnit = array_fill_keys($units, [
            'leadtimeaverage' => 0,
            'memocount' => 0,
            'totalLeadTime' => 0,
            'rank' => null,
            'unitcount' => null,
        ]);

        // Iterate through each new memo
        foreach ($newMemos as $document) {
            // Get lead time units for the current memo
            $leadtimeunits = $document->leadtimeunit();

            // Process lead time for each unit
            foreach ($leadtimeunits as $unitName => $leadtime) {
                if ($leadtime !== null) { // Only consider non-null lead times
                    // Ensure $leadtime is numeric
                    if (is_numeric($leadtime)) {
                        // Ensure memoByUnit[$unitName]['totalLeadTime'] is initialized and numeric
                        if (!isset($memoByUnit[$unitName]['totalLeadTime'])) {
                            $memoByUnit[$unitName]['totalLeadTime'] = 0.0;
                        }

                        // Update total lead time and memo count for the unit
                        $memoByUnit[$unitName]['totalLeadTime'] += (float) $leadtime;
                        $memoByUnit[$unitName]['memocount'] += 1;
                    }
                }
            }

        }

        // Calculate the average lead time for each unit
        foreach ($memoByUnit as $unit => $data) {
            if ($data['memocount'] > 0) {
                // Calculate average lead time in hours
                $memoByUnit[$unit]['leadtimeaverage'] = number_format($data['totalLeadTime'] / $data['memocount'], 2);
            } else {
                $memoByUnit[$unit]['leadtimeaverage'] = null; // No lead time available
            }
        }

        // Remove units containing "Manager"
        foreach ($memoByUnit as $key => $ByUnit) {
            if (strpos($key, 'Manager') !== false || strpos($key, 'MTPR') !== false) {
                unset($memoByUnit[$key]); // Remove element from $memoByUnit based on $key
            }
        }

        // Sort the memoByUnit array by lead time average
        uasort($memoByUnit, function ($a, $b) {
            // Sort by lead time average, treating nulls as the largest value
            return ($a['leadtimeaverage'] ?? PHP_INT_MAX) <=> ($b['leadtimeaverage'] ?? PHP_INT_MAX);
        });

        // Calculate rank and unit count
        $rank = 1;
        foreach ($memoByUnit as $unit => $data) {
            $memoByUnit[$unit]['rank'] = $rank++;
            $memoByUnit[$unit]['unitcount'] = count($memoByUnit); // Count of all units
        }

        return $memoByUnit;
    }

    public static function getstatusunit()
    {
        $newMemos = self::where('documentstatus', 'Terbuka')->get(); // Ambil semua memo dengan status 'Terbuka'
        $dataunit = []; // Inisialisasi array untuk menyimpan data unit

        foreach ($newMemos as $newMemo) {
            // Asumsikan detailonedocument mengembalikan dua nilai: $unitpicvalidation dan $sistem
            $newMemo = $newMemo->detailonedocument();

            $MTPRsend = $newMemo->MTPRsend;
            $operatorsignature = $newMemo->operatorsignature;
            $operatorshare = $newMemo->operatorshare;
            $unitpicvalidation = $newMemo->unitpicvalidation;
            $unitvalidation = $newMemo->unitvalidation;
            $operatorcombinevalidation = $newMemo->operatorcombinevalidation;
            $selfunitvalidation = $newMemo->selfunitvalidation;
            $seniormanagervalidation = $newMemo->seniormanagervalidation;
            $MTPRvalidation = $newMemo->MTPRvalidation;
            $manageroperatorvalidation = $newMemo->manageroperatorvalidation;
            $posisi1 = $newMemo->posisi1;
            $posisi2 = $newMemo->posisi2;
            $posisi3 = $newMemo->posisi3;
            $positionPercentage = $newMemo->positionPercentage;
            $SMname = $newMemo->SMname;
            $unitstepverificator = $newMemo->unitstepverificator;

            $dataunit[$newMemo->documentnumber] = $unitpicvalidation; // Simpan unitpicvalidation berdasarkan nomor dokumen
        }

        return $dataunit; // Kembalikan dataunit
    }


    public static function indexlogic($listproject, $documents, $units)
    {
        // Mengurutkan dokumen berdasarkan created_at (paling lama di atas)
        $documents = $documents->sortBy('created_at');

        // Mendapatkan detail dari setiap dokumen
        foreach ($documents as $document) {
            $newMemo = $document->detailonedocument();

            $MTPRsend = $newMemo->MTPRsend;
            $operatorsignature = $newMemo->operatorsignature;
            $operatorshare = $newMemo->operatorshare;
            $unitpicvalidation = $newMemo->unitpicvalidation;
            $unitvalidation = $newMemo->unitvalidation;
            $operatorcombinevalidation = $newMemo->operatorcombinevalidation;
            $selfunitvalidation = $newMemo->selfunitvalidation;
            $seniormanagervalidation = $newMemo->seniormanagervalidation;
            $MTPRvalidation = $newMemo->MTPRvalidation;
            $manageroperatorvalidation = $newMemo->manageroperatorvalidation;
            $posisi1 = $newMemo->posisi1;
            $posisi2 = $newMemo->posisi2;
            $posisi3 = $newMemo->posisi3;
            $positionPercentage = $newMemo->positionPercentage;
            $SMname = $newMemo->SMname;
            $unitstepverificator = $newMemo->unitstepverificator;
            $withMTPR = $newMemo->withMTPR;

            $document->MTPRsend = $MTPRsend;
            $document->operatorshare = $operatorshare;
            $document->operatorsignature = $operatorsignature;
            $document->unitpicvalidation = $unitpicvalidation;
            $document->unitvalidation = $unitvalidation;
            $document->operatorcombinevalidation = $operatorcombinevalidation;
            $document->selfunitvalidation = $selfunitvalidation;
            $document->seniormanagervalidation = $seniormanagervalidation;
            $document->MTPRvalidation = $MTPRvalidation;
            $document->manageroperatorvalidation = $manageroperatorvalidation;
            $document->positionPercentage = $positionPercentage;
            $document->SMname = $SMname;
            $document->posisi1 = $posisi1;
            $document->posisi2 = $posisi2;
            $document->posisi3 = $posisi3;
            $document->withMTPR = $withMTPR;
        }

        // document pada bagian all
        $revisiall = [];
        $revisiall['All']['documents'] = $documents;

        foreach ($units as $unit) {
            // Mengelompokkan dokumen berdasarkan unit dan project_type_id
            $unitDocuments = $documents->filter(function ($doc) use ($unit) {
                $projectPic = json_decode($doc->project_pic, true);
                if (is_array($projectPic)) {
                    return in_array($unit->name, $projectPic);
                }
                return false;
            });

            // Menambahkan dokumen yang terikat ke unit spesifik
            $revisiall['All']['units'][$unit->name] = $unitDocuments->values()->all();
        }

        // Menambahkan dokumen yang tidak terikat ke unit manapun (unbound)
        $unboundDocuments = $documents->filter(function ($doc) {
            $projectPic = json_decode($doc->project_pic, true);
            // Cek jika project_pic adalah null, array kosong, atau bukan array
            return is_null($projectPic) || (is_array($projectPic) && empty($projectPic));
        });

        // Menambahkan dokumen unbound ke unit berdasarkan operator
        $unboundDocuments->each(function ($doc) use (&$revisiall, $units) {
            foreach ($units as $unit) {
                if ($doc->operator === $unit->name) {
                    // Jika operator cocok dengan unit, tambahkan dokumen ke unit
                    $revisiall['All']['units'][$unit->name][] = $doc;
                    return; // Keluar dari loop setelah menemukan kecocokan
                }
            }
        });



        $revisiall['All']['units']['unbound'] = $unboundDocuments->values()->all();




        foreach ($listproject as $project) {
            $key = str_replace(' ', '_', $project->title);

            // Mengelompokkan dokumen berdasarkan proyek_type_id
            $projectDocuments = $documents->where('proyek_type_id', $project->id);

            // Menyimpan dokumen yang sesuai dengan proyek
            $revisiall[$key]['documents'] = $projectDocuments->values()->all();

            foreach ($units as $unit) {
                // Mengelompokkan dokumen berdasarkan unit dan proyek_type_id
                $unitDocuments = $projectDocuments->filter(function ($doc) use ($unit) {
                    $projectPic = json_decode($doc->project_pic, true);
                    if (is_array($projectPic)) {
                        return in_array($unit->name, $projectPic);
                    }
                    return false;
                });

                // Menambahkan dokumen yang terikat ke unit spesifik
                $revisiall[$key]['units'][$unit->name] = $unitDocuments->values()->all();
            }

            // Menambahkan dokumen yang tidak terikat ke unit manapun (unbound)
            $unboundDocuments = $projectDocuments->filter(function ($doc) {
                $projectPic = json_decode($doc->project_pic, true);
                // Cek jika project_pic adalah null, array kosong, atau bukan array
                return is_null($projectPic) || (is_array($projectPic) && empty($projectPic));
            });

            // Menambahkan dokumen unbound ke unit berdasarkan operator dan menghapus dari unbound
            $unboundDocuments = $unboundDocuments->each(function ($doc) use (&$revisiall, $units, $key) {
                foreach ($units as $unit) {
                    if ($doc->operator === $unit->name) {
                        // Jika operator cocok dengan unit, tambahkan dokumen ke unit
                        $revisiall[$key]['units'][$unit->name][] = $doc;
                        return true; // Tandai dokumen untuk dihapus dari unbound
                    }
                }
                return false; // Tetap di unbound jika tidak cocok
            });




            $revisiall[$key]['units']['unbound'] = $unboundDocuments->values()->all();
        }


        return [$revisiall, $documents];
    }


    public static function getAdditionalDataalldocumentdirect($newMemos)
    {

        $listdatadocuments = [];
        $countterbuka = 0;
        $counttertutup = 0;

        // Loop melalui semua memo yang diambil
        foreach ($newMemos as $memo) {
            // Hitung jumlah dokumen yang terbuka dan tertutup
            $documentstatus = $memo->documentstatus;
            if ($documentstatus == "Terbuka") {
                $countterbuka++;
            } else {
                $counttertutup++;
            }

            // Ambil detail dokumen menggunakan method detailonedocument
            $documentDetails = $memo->detailonedocument();

            // Susun array informasi dokumen
            $Infounit = [
                'document' => json_encode($memo),
                'informasidokumenencoded' => $documentDetails['informasidokumenencoded'],
                'datadikirimencoded' => $documentDetails['datadikirimencoded'],
                'positionPercentage' => $documentDetails['positionPercentage'],
                'unitpicvalidation' => $documentDetails['unitpicvalidation'],
                'projectpics' => $documentDetails['projectpics'],
                'PEsignature' => $documentDetails['PEsignature'],
                'userinformations' => $documentDetails['userinformations'],
                'selfunitvalidation' => $documentDetails['selfunitvalidation'],
                'PEmanagervalidation' => $documentDetails['PEmanagervalidation'],
                'PEcombinework' => $documentDetails['PEcombinework'],
                'PEcombineworkstatus' => $documentDetails['PEcombineworkstatus'],
                'unitvalidation' => $documentDetails['unitvalidation'],
                'status' => $documentDetails['status'],
                'indonesiatimestamps' => $documentDetails['indonesiatimestamps'],
                'level' => $documentDetails['level'],
                'MTPRsend' => $documentDetails['MTPRsend'],
                'PEshare' => $documentDetails['PEshare'],
                'seniormanagervalidation' => $documentDetails['seniormanagervalidation'],
                'MTPRvalidation' => $documentDetails['MTPRvalidation'],
                'MPEvalidation' => $documentDetails['MPEvalidation'],
                'SMname' => $documentDetails['SMname'],
                'arrayprojectpicscount' => $documentDetails['arrayprojectpicscount'],
                'timeline' => $documentDetails['timeline'],
                'parameterlain' => $documentDetails['parameterlain'],
            ];

            // Encode data dokumen dalam format JSON
            $Infounitencode = json_encode($Infounit);
            $listdatadocuments[$memo->id] = $Infounitencode;
        }

        // Hitung persentase dokumen terbuka dan tertutup
        $totalDocuments = $countterbuka + $counttertutup;
        $percentagememoterbuka = $totalDocuments > 0 ? ($countterbuka / $totalDocuments) * 100 : 0;
        $percentagememotertutup = $totalDocuments > 0 ? ($counttertutup / $totalDocuments) * 100 : 0;

        return [
            'listdatadocuments' => $listdatadocuments,
            'percentagememoterbuka' => $percentagememoterbuka,
            'percentagememotertutup' => $percentagememotertutup,
        ];
    }

    public static function percentageandcount($listproject, $revisiall, $projects)
    {
        $newmemos = self::orderBy('created_at', 'desc')->get();
        $titles = $projects->pluck('id', 'title'); // Pluck title as the key and id as the value

        foreach ($listproject as $keyan) {
            if ($keyan != "All") {
                // Get the project type ID
                $projectTypeId = $titles[$keyan] ?? null;

                if ($projectTypeId) {
                    $filteredprogressreports = $newmemos->where('proyek_type_id', $projectTypeId);
                } else {
                    $filteredprogressreports = collect(); // Empty collection if project type not found
                }
            } else {
                $filteredprogressreports = $newmemos;
            }

            // Initialize counts
            $documentTerbuka = 0;
            $documentTertutup = 0;

            // Iterate through the filtered progress reports and count statuses
            foreach ($filteredprogressreports as $doc) {
                $status = strtolower($doc->documentstatus);

                if ($status == 'terbuka' && !is_null($doc->proyek_type_id)) {
                    $documentTerbuka++;
                } elseif ($status == 'tertutup' || $status == '') {
                    $documentTertutup++;
                }
            }

            // Store results in revisiall
            $revisiall[$keyan]['jumlah'] = [
                'terbuka' => $documentTerbuka,
                'tertutup' => $documentTertutup
            ];

            // Calculate percentages
            $totaldocument = $documentTerbuka + $documentTertutup;
            $positifnewreport = $totaldocument > 0 ? ($documentTerbuka / $totaldocument) * 100 : 0;

            $revisiall[$keyan]['persentase'] = [
                'terbuka' => $positifnewreport,
                'tertutup' => 100 - $positifnewreport
            ];
        }

        return $revisiall;
    }







}
