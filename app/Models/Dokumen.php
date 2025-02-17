<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumens';

    protected $fillable = [
        'information',
        'remaininformation',
        'asliordummy',
        'timeline',
        'memokind',
        'memoorigin',
        'userinformations',
        'documentnumber',
        'documentstatus',
        'times',
        'underpereadstatus',
        'pereadstatus',
        'feedbacktimestamp',
        'filenames',
        'metadatas',
        'authors',
        'linkfiles',
        'category',
        'project_type',
        'project_pic',
        'documentname',
    ];

    // Define relationship to TugasDivisi
    public function tugasDivisi()
    {
        return $this->hasMany(TugasDivisi::class, 'dokumen_id');
    }

    public function detailonedocument()
    {
        $temporaryrule = "GUEST";
        if (isset(auth()->user()->rule)) {
            $temporaryrule = auth()->user()->rule;
        }
        $informasidokumen = [];
        $informasidokumen['documentname'] = $this->documentname;
        $informasidokumen['documentnumber'] = $this->documentnumber;
        $informasidokumen['memokind'] = $this->memokind;
        $informasidokumen['memoorigin'] = $this->memoorigin;
        $informasidokumen['documentstatus'] = $this->documentstatus;
        $informasidokumen['category'] = $this->category;
        $informasidokumenencoded = json_encode($informasidokumen);
        $timeline = json_decode($this->timeline, true);



        $userinformations = json_decode($this->userinformations);
        $status = "";
        $indonesiatimestamps = [];
        $level = '';
        $MTPRsend = "Aktif"; // Tambah variabel baru
        $PEshare = "Nonaktif";
        $PEmanagervalidation = "Nonaktif"; // Tambah variabel baru
        $seniormanagervalidation = "Nonaktif"; // Tambah variabel baru
        $MTPRvalidation = "Nonaktif"; // Tambah variabel baru
        $PEcombinework = "Nonaktif"; // Tambah variabel baru
        $MPEvalidation = "Tidak Terlibat"; // Tambah variabel baru
        $selfunitvalidation = "Nonaktif"; // Tambah variabel baru
        $unitvalidation = "Nonaktif"; // Tambah variabel baru
        $PEsignature = "Nonaktif"; // Tambah variabel baru
        $PEcombineworkstatus = "Belum ada review";
        $SMname = "Belum ditentukan";
        $projectpics = [];
        if (!empty($this->project_pic)) {
            $projectpics = json_decode($this->project_pic);
        }


        $arrayprojectpicscount = [];
        $unitpicvalidation = [];

        // Membuat list yang berisi array keseluruhan $projectpics dengan status nonaktif
        if (!empty($this->project_pic)) {
            foreach ($projectpics as $picname) {
                $lokalarray = [];
                $lokalarray[$picname] = "Nonaktif";
                $unitpicvalidation[$picname] = "Nonaktif";
                $arrayprojectpicscount[] = $lokalarray;
            }
        }


        for ($i = 0; $i < count($userinformations); $i++) {
            $cekinformasiuser = json_decode($userinformations[$i]) ?? [];
            if ($cekinformasiuser != "") {
                $data = $cekinformasiuser;
                $picname = $data->pic;
                $levelname = $data->level;


                $sumberinformasi = $cekinformasiuser->userinformations;
                $userInfo = json_decode($sumberinformasi, true);

                //unitvalidation
                if (!empty($projectpics)) {
                    $PEshare = "Aktif";
                    $statuspersetujuan_unitvalidation = [
                        "1" => "Tidak",
                        "2" => "Tidak",
                    ];
                    $statuspersetujuan_unitvalidation["1"] = "Ya";
                    $nilaiinformasi = $userInfo['conditionoffile'];
                    if (in_array($picname, $projectpics) && ($nilaiinformasi == "Approved" || $nilaiinformasi == "Approved by Manager")) {
                        $statuspersetujuan_unitvalidation["2"] = "Ya";
                    }
                    $nilaiinformasi = $userInfo['conditionoffile2'] ?? '';
                    if (in_array($picname, $projectpics) && $nilaiinformasi == "feedback") {
                        //jika pic ada maka otomatis status ongoing
                        $unitpicvalidation[$picname] = "Ongoing";
                    }
                    if ($statuspersetujuan_unitvalidation["1"] == "Ya" && $statuspersetujuan_unitvalidation["2"] == "Ya") {
                        $unitpicvalidation[$picname] = "Aktif";
                    }
                }


                //selfunitvalidation awal
                $statuspersetujuan_selfunitvalidation = ["1" => "Tidak", "2" => "Tidak",];
                if ($temporaryrule == "Product Engineering") {
                    $conditionoffile2 = $userInfo['conditionoffile2'] ?? "";
                    if (($picname == $temporaryrule || $levelname == $temporaryrule) && $conditionoffile2 == "combine") {
                        $selfunitvalidation = "Aktif";
                    }

                }
                $statuspersetujuan_selfunitvalidation["1"] = "Ya";
                $conditionoffile = $userInfo['conditionoffile'];
                ;
                if (($picname == $temporaryrule || $levelname == $temporaryrule) && ($conditionoffile == "Approved" || $conditionoffile == "Approved by Manager")) {
                    $statuspersetujuan_selfunitvalidation["2"] = "Ya";
                }
                if ($statuspersetujuan_selfunitvalidation["1"] == "Ya" && $statuspersetujuan_selfunitvalidation["2"] == "Ya") {
                    $selfunitvalidation = "Aktif";
                }
                //selfunitvalidation akhir




                //PEcombinework
                $nilaiinformasi = $userInfo['conditionoffile2'] ?? "";
                if (in_array($nilaiinformasi, ["combine"])) {
                    $PEcombinework = "Aktif";
                    $PEcombineworkstatus = $userInfo['hasilreview'] ?? "";
                }

                //PEsignature
                if (in_array($picname, ["Product Engineering"]) && in_array($levelname, ["signature"])) {
                    $PEsignature = "Aktif";
                }


                //PEmanagervalidation
                if ($picname == "Product Engineering") {
                    $nilaiinformasi = $userInfo['conditionoffile2'];
                    if ($nilaiinformasi == "combine") {
                        if ($PEmanagervalidation == "Nonaktif") {
                            $PEmanagervalidation = "Ongoing";
                        }
                    }
                }
                if (in_array($picname, ["Product Engineering"]) && in_array($levelname, ["Manager Product Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"])) {
                    $PEmanagervalidation = "Aktif";
                }




                //seniormanagervalidation
                if (in_array($picname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"]) && in_array($levelname, ["MTPR"])) {
                    $seniormanagervalidation = "Aktif";
                }



                //MPEACTIVATION
                if (in_array($levelname, ["Manager Product Engineering"]) && $userInfo['conditionoffile2'] == "combine") {
                    $MPEvalidation = "Terlibat";
                    if ($MPEvalidation = "Terlibat") {
                        $SMname = "Senior Manager Engineering";
                        if ($MPEvalidation = "Terlibat")
                            //MPEValidation
                            if ($picname == "Manager Product Engineering" && $levelname == "Senior Manager Engineering") {
                                $antarkondisi = "Aktif";
                            }
                    }
                } elseif (in_array($levelname, ["Senior Manager Desain"])) {
                    $SMname = "Senior Manager Desain";
                } elseif (in_array($levelname, ["Senior Manager Teknologi Produksi"])) {
                    $SMname = "Senior Manager Teknologi Produksi";
                }




                //MTPRvalidation
                if (in_array($picname, ["MTPR"]) && in_array($levelname, ["selesai"])) {
                    $MTPRvalidation = "Aktif";
                }

            }
        }


        // Check apakah semua unitpicvalidation adalah "Aktif" menggunakan array_filter()
        $activeValidations = array_filter($unitpicvalidation, function ($value) {
            return $value == "Aktif";
        });

        if (count($unitpicvalidation) == count($activeValidations)) {
            if (!empty($this->project_pic)) {
                $unitvalidation = "Aktif";
                if ($unitvalidation == "Aktif") { // Perbaiki penugasan nilai di sini
                    if ($PEmanagervalidation == 'Nonaktif') {
                        $nama_divisi = "Product Engineering";
                        if (isset($timeline[$nama_divisi . '_combine' . '_read'])) {
                            $PEmanagervalidation = 'Sudah dibaca';
                        } else {
                            $PEmanagervalidation = 'Belum dibaca';
                        }
                    }
                }
            }



            // Periksa apakah pengguna adalah PE dan pereadstatus adalah null
            if ($temporaryrule != "Product Engineering" && is_null($this->pereadstatus)) {
                $this->update([
                    'pereadstatus' => now(),
                ]);
                // Cari tugas divisi yang pertama ditemukan
                $file = TugasDivisi::whereRaw('CAST(dokumen_id AS CHAR) = ?', [$this->id])
                    ->where('nama_divisi', 'Product Engineering')
                    ->first();

                // Jika tugas divisi ditemukan, update status sudah dibaca
                if ($file) {
                    $file->update([
                        'sudahdibaca' => "belum dibaca",
                    ]);
                }
            }
        }



        $totalSteps = 5; // Jumlah total langkah yang harus diselesaikan
        $completedSteps = 0; // Inisialisasi jumlah langkah yang sudah selesai

        if ($MTPRsend == 'Aktif') {
            $completedSteps++;
            if ($PEshare == 'Nonaktif') {
                $PEshare = 'Belum dibaca';
                $nama_divisi = "Product Engineering";
                if (isset($timeline[$nama_divisi . '_share' . '_read'])) {
                    $PEshare = 'Ongoing';
                }
            }
        }
        if ($PEshare == 'Aktif') {
            $completedSteps++;
            if (isset($projectpics)) {
                if (!empty($this->project_pic)) {
                    foreach ($projectpics as $picname) {
                        if ($unitpicvalidation[$picname] == "Nonaktif") {
                            $unitpicvalidation[$picname] = "Belum dibaca";
                            if (isset($timeline[$picname . '_unit' . '_read'])) {
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

        if ($PEmanagervalidation == 'Aktif') {
            $completedSteps++;
            if ($MPEvalidation == "Tidak Terlibat") {
                if ($seniormanagervalidation == 'Nonaktif') {
                    $seniormanagervalidation = 'Belum dibaca';
                    $nama_divisis = ["Senior Manager Teknologi Produksi", "Senior Manager Engineering", "Senior Manager Desain",];
                    for ($k = 0; $k < count($nama_divisis); $k++) {
                        if (isset($timeline[$nama_divisis[$k] . '_seniorvalid' . '_read'])) {
                            $seniormanagervalidation = "Ongoing";
                            break;
                        }
                    }
                }
            } elseif ($MPEvalidation == "Terlibat") {
                $MPEvalidation = 'Belum dibaca';
                if (isset($timeline["Manager Product Engineering" . '_unit' . '_read'])) {
                    $MPEvalidation = 'Sudah dibaca';
                    if ($antarkondisi = "Aktif") {
                        $MPEvalidation = 'Sudah dibaca';
                        if (isset($antarkondisi)) {
                            if ($antarkondisi == "Aktif") {
                                $MPEvalidation = "Aktif";
                            }

                        }
                    }

                }
                if ($MPEvalidation == 'Aktif') {
                    if ($seniormanagervalidation == 'Nonaktif') {
                        $seniormanagervalidation = 'Belum dibaca';
                        $nama_divisis = ["Senior Manager Teknologi Produksi", "Senior Manager Engineering", "Senior Manager Desain",];
                        for ($k = 0; $k < count($nama_divisis); $k++) {
                            if (isset($timeline[$nama_divisis[$k] . '_seniorvalid' . '_read'])) {
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
                if (isset($timeline[$nama_divisi . '_finish' . '_read'])) {
                    $MTPRvalidation = 'Ongoing';
                }
            }

        }

        if ($MTPRvalidation == 'Aktif') {
            $completedSteps++;
        }


        $positionPercentage = intval(($completedSteps / $totalSteps) * 100); // Mengonversi ke integer
        if ($this->documentstatus == "Tertutup") {
            if ($MTPRvalidation != "Aktif") {
                $PEcombineworkstatus = "Status belum didefenisikan";
            }
            // 'documentshared' memiliki nilai
            $positionPercentage = 100;
            $MTPRsend = "Aktif"; // Tambah variabel baru
            $PEshare = "Aktif"; // Tambah variabel baru
            if ($MPEvalidation = "Terlibat") {
                $MPEvalidation = "Aktif";
            }
            $PEmanagervalidation = "Aktif"; // Tambah variabel baru
            $seniormanagervalidation = "Aktif"; // Tambah variabel baru
            $MTPRvalidation = "Aktif"; // Tambah variabel baru
            $selfunitvalidation = "Aktif"; // Tambah variabel baru
            $unitvalidation = "Aktif"; // Tambah variabel baru
            $PEsignature = "Aktif"; // Tambah variabel baru    
            if (isset($projectpics)) {
                if (!empty($this->project_pic)) {
                    foreach ($projectpics as $picname) {
                        $lokalarray = [];
                        $lokalarray[$picname] = "Nonaktif";
                        $unitpicvalidation[$picname] = "Aktif";
                        $arrayprojectpicscount[] = $lokalarray;
                    }
                }
            }
        }

        $listdata = [];
        $userinformations = json_decode($this->userinformations, true);
        $count = count($userinformations);

        for ($su = 0; $su < $count; $su++) {
            $datadikirim = [];
            $ringkasan = (json_decode($this->userinformations, true)[$su]);
            $datalokal = [];
            $sumberdata = json_decode(json_decode($ringkasan)->userinformations);
            $datadikirim['pic'] = json_decode($ringkasan)->pic;
            $datadikirim['level'] = json_decode($ringkasan)->level;
            $datalokal['nama penulis'] = isset($sumberdata->{'nama penulis'}) ? $sumberdata->{'nama penulis'} : $sumberdata->{'nama'};
            $datalokal['email'] = isset($sumberdata->{'email'}) ? $sumberdata->{'email'} : null;
            $datalokal['conditionoffile'] = isset($sumberdata->{'conditionoffile'}) ? $sumberdata->{'conditionoffile'} : null;
            $datalokal['conditionoffile2'] = isset($sumberdata->{'conditionoffile2'}) ? $sumberdata->{'conditionoffile2'} : null;
            $datalokal['hasilreview'] = isset($sumberdata->{'hasilreview'}) ? $sumberdata->{'hasilreview'} : null;
            $datalokal['sudahdibaca'] = isset($sumberdata->{'sudahdibaca'}) ? $sumberdata->{'sudahdibaca'} : null;
            $datalokal['listfilenames'] = isset(json_decode($ringkasan)->listfilenames) ? json_decode($ringkasan)->listfilenames : [];
            $datalokal['listmetadatas'] = isset(json_decode($ringkasan)->listmetadatas) ? json_decode($ringkasan)->listmetadatas : [];
            $datalokal['listlinkfiles'] = isset(json_decode($ringkasan)->listlinkfiles) ? json_decode($ringkasan)->listlinkfiles : [];
            $datadikirim['userinformations'] = $datalokal;
            $listdata[] = $datadikirim;
        }
        $datadikirimencoded = json_encode($listdata);
        $posisi1 = "on";
        $posisi2 = "off";
        $posisi3 = "off";
        if ($PEshare == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "on";
            $posisi3 = "off";
        }


        if ($unitvalidation == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "off";
            $posisi3 = "on";
        }
        $posisi = [
            'posisi1' => $posisi1,
            'posisi2' => $posisi2,
            'posisi3' => $posisi3,
        ];
        $parameterlain = [
            'posisi' => $posisi,
        ];
        return [$timeline, $informasidokumenencoded, $datadikirimencoded, $positionPercentage, $unitpicvalidation, $projectpics, $PEsignature, $userinformations, $selfunitvalidation, $PEmanagervalidation, $PEcombinework, $PEcombineworkstatus, $unitvalidation, $status, $indonesiatimestamps, $level, $MTPRsend, $PEshare, $seniormanagervalidation, $MTPRvalidation, $MPEvalidation, $SMname, $arrayprojectpicscount, $parameterlain];

    }



    public function timelinedecode()
    {
        return json_decode($this->timeline, true);
    }

    public function remaininformasidecode()
    {
        return json_decode($this->remaininformation, true);
    }


    public static function percentageandcount($listproject, $revisiall)
    {
        $progressreports = Dokumen::orderBy('created_at', 'desc')->get();
        foreach ($listproject as $keyan) {
            //progressreport persentase dan jumlah
            if ($keyan != "All") {
                $filteredprogressreports = collect($progressreports)->where('project_type', $keyan)->all();
            } else {
                $filteredprogressreports = $progressreports;
            }
            $documentTerbuka = collect($filteredprogressreports)->filter(function ($doc) {
                return strtolower($doc['documentstatus']) == 'terbuka' && !is_null($doc['project_type']) && $doc['project_type'] !== '';
            })->count();

            $documentTertutup = collect($filteredprogressreports)->filter(function ($doc) {
                $status = strtolower($doc['documentstatus']);
                return ($status == 'tertutup' || $status == '') && !is_null($doc['project_type']) && $doc['project_type'] !== '';
            })->count();
            $revisiall[$keyan]['jumlah'] = [
                'terbuka' => $documentTerbuka,
                'tertutup' => $documentTertutup
            ];

            $totaldocument = $documentTerbuka + $documentTertutup;
            $positifnewreport = $totaldocument > 0 ? $documentTerbuka / $totaldocument * 100 : 0;
            $revisiall[$keyan]['persentase'] = [
                'terbuka' => $positifnewreport,
                'tertutup' => 100 - $positifnewreport
            ];
        }
        return $revisiall;
    }


    public static function getAdditionalDataonedocumentdirect($document)
    {


        $temporaryrule = "GUEST";
        if (isset(auth()->user()->rule)) {
            $temporaryrule = auth()->user()->rule;
        }
        $informasidokumen = [];
        $informasidokumen['documentname'] = $document->documentname;
        $informasidokumen['documentnumber'] = $document->documentnumber;
        $informasidokumen['memokind'] = $document->memokind;
        $informasidokumen['memoorigin'] = $document->memoorigin;
        $informasidokumen['documentstatus'] = $document->documentstatus;
        $informasidokumen['category'] = $document->category;
        $informasidokumenencoded = json_encode($informasidokumen);
        $timeline = json_decode($document->timeline, true);



        $userinformations = json_decode($document->userinformations);
        $status = "";
        $indonesiatimestamps = [];
        $level = '';
        $MTPRsend = "Aktif"; // Tambah variabel baru
        $PEshare = "Nonaktif";
        $PEmanagervalidation = "Nonaktif"; // Tambah variabel baru
        $seniormanagervalidation = "Nonaktif"; // Tambah variabel baru
        $MTPRvalidation = "Nonaktif"; // Tambah variabel baru
        $PEcombinework = "Nonaktif"; // Tambah variabel baru
        $MPEvalidation = "Tidak Terlibat"; // Tambah variabel baru
        $selfunitvalidation = "Nonaktif"; // Tambah variabel baru
        $unitvalidation = "Nonaktif"; // Tambah variabel baru
        $PEsignature = "Nonaktif"; // Tambah variabel baru
        $PEcombineworkstatus = "Belum ada review";
        $SMname = "Belum ditentukan";
        $projectpics = [];
        if (!empty($document->project_pic)) {
            $projectpics = json_decode($document->project_pic);
        }


        $arrayprojectpicscount = [];
        $unitpicvalidation = [];

        // Membuat list yang berisi array keseluruhan $projectpics dengan status nonaktif
        if (!empty($document->project_pic)) {
            foreach ($projectpics as $picname) {
                $lokalarray = [];
                $lokalarray[$picname] = "Nonaktif";
                $unitpicvalidation[$picname] = "Nonaktif";
                $arrayprojectpicscount[] = $lokalarray;
            }
        }


        for ($i = 0; $i < count($userinformations); $i++) {
            $cekinformasiuser = json_decode($userinformations[$i]) ?? [];
            if ($cekinformasiuser != "") {
                $data = $cekinformasiuser;
                $picname = $data->pic;
                $levelname = $data->level;


                $sumberinformasi = $cekinformasiuser->userinformations;
                $userInfo = json_decode($sumberinformasi, true);
                //unitvalidation
                if (!empty($projectpics)) {
                    $PEshare = "Aktif";
                    $statuspersetujuan_unitvalidation = [
                        "1" => "Tidak",
                        "2" => "Tidak",
                    ];
                    $statuspersetujuan_unitvalidation["1"] = "Ya";
                    $nilaiinformasi = $userInfo['conditionoffile'];
                    if (in_array($picname, $projectpics) && ($nilaiinformasi == "Approved" || $nilaiinformasi == "Approved by Manager")) {
                        $statuspersetujuan_unitvalidation["2"] = "Ya";
                    }
                    $nilaiinformasi = $userInfo['conditionoffile2'] ?? '';
                    if (in_array($picname, $projectpics) && $nilaiinformasi == "feedback") {
                        //jika pic ada maka otomatis status ongoing
                        $unitpicvalidation[$picname] = "Ongoing";
                    }
                    if ($statuspersetujuan_unitvalidation["1"] == "Ya" && $statuspersetujuan_unitvalidation["2"] == "Ya") {
                        $unitpicvalidation[$picname] = "Aktif";
                    }
                }
                //selfunitvalidation awal
                $statuspersetujuan_selfunitvalidation = ["1" => "Tidak", "2" => "Tidak",];
                if ($temporaryrule == "Product Engineering") {
                    $conditionoffile2 = $userInfo['conditionoffile2'] ?? "";
                    if (($picname == $temporaryrule || $levelname == $temporaryrule) && $conditionoffile2 == "combine") {
                        $selfunitvalidation = "Aktif";
                    }

                }
                $statuspersetujuan_selfunitvalidation["1"] = "Ya";
                $conditionoffile = $userInfo['conditionoffile'];
                ;
                if (($picname == $temporaryrule || $levelname == $temporaryrule) && ($conditionoffile == "Approved" || $conditionoffile == "Approved by Manager")) {
                    $statuspersetujuan_selfunitvalidation["2"] = "Ya";
                }
                if ($statuspersetujuan_selfunitvalidation["1"] == "Ya" && $statuspersetujuan_selfunitvalidation["2"] == "Ya") {
                    $selfunitvalidation = "Aktif";
                }
                //selfunitvalidation akhir




                //PEcombinework
                $nilaiinformasi = $userInfo['conditionoffile2'] ?? "";
                if (in_array($nilaiinformasi, ["combine"])) {
                    $PEcombinework = "Aktif";
                    $PEcombineworkstatus = $userInfo['hasilreview'] ?? "";
                }

                //PEsignature
                if (in_array($picname, ["Product Engineering"]) && in_array($levelname, ["signature"])) {
                    $PEsignature = "Aktif";
                }


                //PEmanagervalidation
                if ($picname == "Product Engineering") {
                    $nilaiinformasi = $userInfo['conditionoffile2'];
                    if ($nilaiinformasi == "combine") {
                        if ($PEmanagervalidation == "Nonaktif") {
                            $PEmanagervalidation = "Ongoing";
                        }
                    }
                }
                if (in_array($picname, ["Product Engineering"]) && in_array($levelname, ["Manager Product Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"])) {
                    $PEmanagervalidation = "Aktif";
                }




                //seniormanagervalidation
                if (in_array($picname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"]) && in_array($levelname, ["MTPR"])) {
                    $seniormanagervalidation = "Aktif";
                }



                //MPEACTIVATION
                if (in_array($levelname, ["Manager Product Engineering"]) && $userInfo['conditionoffile2'] == "combine") {
                    $MPEvalidation = "Terlibat";
                    if ($MPEvalidation = "Terlibat") {
                        $SMname = "Senior Manager Engineering";
                        if ($MPEvalidation = "Terlibat")
                            //MPEValidation
                            if ($picname == "Manager Product Engineering" && $levelname == "Senior Manager Engineering") {
                                $antarkondisi = "Aktif";
                            }
                    }
                } elseif (in_array($levelname, ["Senior Manager Desain"])) {
                    $SMname = "Senior Manager Desain";
                } elseif (in_array($levelname, ["Senior Manager Teknologi Produksi"])) {
                    $SMname = "Senior Manager Teknologi Produksi";
                }




                //MTPRvalidation
                if (in_array($picname, ["MTPR"]) && in_array($levelname, ["selesai"])) {
                    $MTPRvalidation = "Aktif";
                }

            }
        }


        // Check apakah semua unitpicvalidation adalah "Aktif" menggunakan array_filter()
        $activeValidations = array_filter($unitpicvalidation, function ($value) {
            return $value == "Aktif";
        });

        if (count($unitpicvalidation) == count($activeValidations)) {
            if (!empty($document->project_pic)) {
                $unitvalidation = "Aktif";
                if ($unitvalidation == "Aktif") { // Perbaiki penugasan nilai di sini
                    if ($PEmanagervalidation == 'Nonaktif') {
                        $nama_divisi = "Product Engineering";
                        if (isset($timeline[$nama_divisi . '_combine' . '_read'])) {
                            $PEmanagervalidation = 'Sudah dibaca';
                        } else {
                            $PEmanagervalidation = 'Belum dibaca';
                        }
                    }
                }
            }



            // Periksa apakah pengguna adalah PE dan pereadstatus adalah null
            if ($temporaryrule != "Product Engineering" && is_null($document->pereadstatus)) {
                $document->update([
                    'pereadstatus' => now(),
                ]);
                // Cari tugas divisi yang pertama ditemukan
                $file = TugasDivisi::whereRaw('CAST(dokumen_id AS CHAR) = ?', [$document->id])
                    ->where('nama_divisi', 'Product Engineering')
                    ->first();

                // Jika tugas divisi ditemukan, update status sudah dibaca
                if ($file) {
                    $file->update([
                        'sudahdibaca' => "belum dibaca",
                    ]);
                }
            }
        }



        $totalSteps = 5; // Jumlah total langkah yang harus diselesaikan
        $completedSteps = 0; // Inisialisasi jumlah langkah yang sudah selesai

        if ($MTPRsend == 'Aktif') {
            $completedSteps++;
            if ($PEshare == 'Nonaktif') {
                $PEshare = 'Belum dibaca';
                $nama_divisi = "Product Engineering";
                if (isset($timeline[$nama_divisi . '_share' . '_read'])) {
                    $PEshare = 'Ongoing';
                }
            }
        }
        if ($PEshare == 'Aktif') {
            $completedSteps++;
            if (isset($projectpics)) {
                if (!empty($document->project_pic)) {
                    foreach ($projectpics as $picname) {
                        if ($unitpicvalidation[$picname] == "Nonaktif") {
                            $unitpicvalidation[$picname] = "Belum dibaca";
                            if (isset($timeline[$picname . '_unit' . '_read'])) {
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

        if ($PEmanagervalidation == 'Aktif') {
            $completedSteps++;
            if ($MPEvalidation == "Tidak Terlibat") {
                if ($seniormanagervalidation == 'Nonaktif') {
                    $seniormanagervalidation = 'Belum dibaca';
                    $nama_divisis = ["Senior Manager Teknologi Produksi", "Senior Manager Engineering", "Senior Manager Desain",];
                    for ($k = 0; $k < count($nama_divisis); $k++) {
                        if (isset($timeline[$nama_divisis[$k] . '_seniorvalid' . '_read'])) {
                            $seniormanagervalidation = "Ongoing";
                            break;
                        }
                    }
                }
            } elseif ($MPEvalidation == "Terlibat") {
                $MPEvalidation = 'Belum dibaca';
                if (isset($timeline["Manager Product Engineering" . '_unit' . '_read'])) {
                    $MPEvalidation = 'Sudah dibaca';
                    if ($antarkondisi = "Aktif") {
                        $MPEvalidation = 'Sudah dibaca';
                        if (isset($antarkondisi)) {
                            if ($antarkondisi == "Aktif") {
                                $MPEvalidation = "Aktif";
                            }

                        }
                    }

                }
                if ($MPEvalidation == 'Aktif') {
                    if ($seniormanagervalidation == 'Nonaktif') {
                        $seniormanagervalidation = 'Belum dibaca';
                        $nama_divisis = ["Senior Manager Teknologi Produksi", "Senior Manager Engineering", "Senior Manager Desain",];
                        for ($k = 0; $k < count($nama_divisis); $k++) {
                            if (isset($timeline[$nama_divisis[$k] . '_seniorvalid' . '_read'])) {
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
                if (isset($timeline[$nama_divisi . '_finish' . '_read'])) {
                    $MTPRvalidation = 'Ongoing';
                }
            }

        }

        if ($MTPRvalidation == 'Aktif') {
            $completedSteps++;
        }


        $positionPercentage = intval(($completedSteps / $totalSteps) * 100); // Mengonversi ke integer
        if ($document->documentstatus == "Tertutup") {
            if ($MTPRvalidation != "Aktif") {
                $PEcombineworkstatus = "Status belum didefenisikan";
            }
            // 'documentshared' memiliki nilai
            $positionPercentage = 100;
            $MTPRsend = "Aktif"; // Tambah variabel baru
            $PEshare = "Aktif"; // Tambah variabel baru
            if ($MPEvalidation = "Terlibat") {
                $MPEvalidation = "Aktif";
            }
            $PEmanagervalidation = "Aktif"; // Tambah variabel baru
            $seniormanagervalidation = "Aktif"; // Tambah variabel baru
            $MTPRvalidation = "Aktif"; // Tambah variabel baru
            $selfunitvalidation = "Aktif"; // Tambah variabel baru
            $unitvalidation = "Aktif"; // Tambah variabel baru
            $PEsignature = "Aktif"; // Tambah variabel baru    
            if (isset($projectpics)) {
                if (!empty($document->project_pic)) {
                    foreach ($projectpics as $picname) {
                        $lokalarray = [];
                        $lokalarray[$picname] = "Nonaktif";
                        $unitpicvalidation[$picname] = "Aktif";
                        $arrayprojectpicscount[] = $lokalarray;
                    }
                }
            }
        }

        $listdata = [];
        $userinformations = json_decode($document->userinformations, true);
        $count = count($userinformations);

        for ($su = 0; $su < $count; $su++) {
            $datadikirim = [];
            $ringkasan = (json_decode($document->userinformations, true)[$su]);
            $datalokal = [];
            $sumberdata = json_decode(json_decode($ringkasan)->userinformations);
            $datadikirim['pic'] = json_decode($ringkasan)->pic;
            $datadikirim['level'] = json_decode($ringkasan)->level;
            $datalokal['nama penulis'] = isset($sumberdata->{'nama penulis'}) ? $sumberdata->{'nama penulis'} : $sumberdata->{'nama'};
            $datalokal['email'] = isset($sumberdata->{'email'}) ? $sumberdata->{'email'} : null;
            $datalokal['conditionoffile'] = isset($sumberdata->{'conditionoffile'}) ? $sumberdata->{'conditionoffile'} : null;
            $datalokal['conditionoffile2'] = isset($sumberdata->{'conditionoffile2'}) ? $sumberdata->{'conditionoffile2'} : null;
            $datalokal['hasilreview'] = isset($sumberdata->{'hasilreview'}) ? $sumberdata->{'hasilreview'} : null;
            $datalokal['sudahdibaca'] = isset($sumberdata->{'sudahdibaca'}) ? $sumberdata->{'sudahdibaca'} : null;
            $datalokal['listfilenames'] = isset(json_decode($ringkasan)->listfilenames) ? json_decode($ringkasan)->listfilenames : [];
            $datalokal['listmetadatas'] = isset(json_decode($ringkasan)->listmetadatas) ? json_decode($ringkasan)->listmetadatas : [];
            $datalokal['listlinkfiles'] = isset(json_decode($ringkasan)->listlinkfiles) ? json_decode($ringkasan)->listlinkfiles : [];
            $datadikirim['userinformations'] = $datalokal;
            $listdata[] = $datadikirim;
        }
        $datadikirimencoded = json_encode($listdata);
        $posisi1 = "on";
        $posisi2 = "off";
        $posisi3 = "off";
        if ($PEshare == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "on";
            $posisi3 = "off";
        }


        if ($unitvalidation == "Aktif") {
            $posisi1 = "off";
            $posisi2 = "off";
            $posisi3 = "on";
        }
        $posisi = [
            'posisi1' => $posisi1,
            'posisi2' => $posisi2,
            'posisi3' => $posisi3,
        ];
        $parameterlain = [
            'posisi' => $posisi,
        ];
        return [$timeline, $informasidokumenencoded, $datadikirimencoded, $positionPercentage, $unitpicvalidation, $projectpics, $PEsignature, $userinformations, $selfunitvalidation, $PEmanagervalidation, $PEcombinework, $PEcombineworkstatus, $unitvalidation, $status, $indonesiatimestamps, $level, $MTPRsend, $PEshare, $seniormanagervalidation, $MTPRvalidation, $MPEvalidation, $SMname, $arrayprojectpicscount, $parameterlain];

    }

    public static function getAdditionalDataalldocumentdirect($documents)
    {
        $listdatadocuments = [];
        $countterbuka = 0;
        $counttertutup = 0;
        for ($i = 0; $i < count($documents); $i++) {
            $documentstatus = $documents[$i]->documentstatus;
            if ($documentstatus == "Terbuka") {
                $countterbuka++;
            } else {
                $counttertutup++;
            }
            $document = $documents[$i];
            [$timeline, $informasidokumenencoded, $datadikirimencoded, $positionPercentage, $unitpicvalidation, $projectpics, $PEsignature, $userinformations, $selfunitvalidation, $PEmanagervalidation, $PEcombinework, $PEcombineworkstatus, $unitvalidation, $status, $indonesiatimestamps, $level, $MTPRsend, $PEshare, $seniormanagervalidation, $MTPRvalidation, $MPEvalidation, $SMname, $arrayprojectpicscount, $parameterlain] = Dokumen::getAdditionalDataonedocumentdirect($documents[$i]);
            $Infounit = [
                'document' => json_encode($document),
                'informasidokumenencoded' => $informasidokumenencoded,
                'datadikirimencoded' => $datadikirimencoded,
                'positionPercentage' => $positionPercentage,
                'unitpicvalidation' => $unitpicvalidation,
                'projectpics' => $projectpics,
                'PEsignature' => $PEsignature,
                'userinformations' => $userinformations,
                'selfunitvalidation' => $selfunitvalidation,
                'PEmanagervalidation' => $PEmanagervalidation,
                'PEcombinework' => $PEcombinework,
                'PEcombineworkstatus' => $PEcombineworkstatus,
                'unitvalidation' => $unitvalidation,
                'status' => $status,
                'indonesiatimestamps' => $indonesiatimestamps,
                'level' => $level,
                'MTPRsend' => $MTPRsend,
                'PEshare' => $PEshare,
                'seniormanagervalidation' => $seniormanagervalidation,
                'MTPRvalidation' => $MTPRvalidation,
                'MPEvalidation' => $MPEvalidation,
                'SMname' => $SMname,
                'arrayprojectpicscount' => $arrayprojectpicscount,
                'timeline' => $timeline,
                'parameterlain' => $parameterlain,
            ];
            $Infounitencode = json_encode($Infounit);
            $listdatadocuments[$document->id] = $Infounitencode;
        }
        $percentagememoterbuka = ($countterbuka / ($countterbuka + $counttertutup)) * 100;
        $percentagememotertutup = ($counttertutup / ($countterbuka + $counttertutup)) * 100;
        $listdatadocumentencode = json_encode($listdatadocuments);
        return [$listdatadocumentencode, $percentagememoterbuka, $percentagememotertutup];
    }

    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }

}