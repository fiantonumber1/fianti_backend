<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class Newreport extends Model
{
    protected $fillable = [
        'unit',
        'proyek_type',
        'proyek_type_id',
        'status',
        'linkscript',
        'linkspreadsheet',
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }

    public function newprogressreports()
    {
        return $this->hasMany(Newprogressreport::class);
    }

    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }

    public function calculateLevelStatusData($progressReports)
    {
        $datalevel = [
            'all' => [
                'Predesign' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                'Intermediate Design' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                'Final Design' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                'Belum Diidentifikasi' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
            ]
        ];
        $datastatus = [
            'all' => ["Working Progress" => 0, "RELEASED" => 0, "Belum Dimulai" => 0]
        ];

        foreach ($progressReports as $item) {
            $level = isset($item['level']) && !empty($item['level']) ? $item['level'] : 'Belum Diidentifikasi';
            $status = isset($item['status']) && !empty($item['status']) ? $item['status'] : 'Belum Dimulai';
            $drafter = isset($item['drafter']) && !empty($item['drafter']) ? str_replace(' ', '_', $item['drafter']) : 'unknown';

            if (!isset($datalevel[$drafter])) {
                $datalevel[$drafter] = [
                    'Predesign' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                    'Intermediate Design' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                    'Final Design' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                    'Belum Diidentifikasi' => ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0],
                ];
            }
            if (!isset($datastatus[$drafter])) {
                $datastatus[$drafter] = ["Working Progress" => 0, "RELEASED" => 0, "Belum Dimulai" => 0];
            }

            if (!isset($datalevel[$drafter][$level])) {
                $datalevel[$drafter][$level] = ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0];
            }
            if (!isset($datalevel[$drafter][$level][$status])) {
                $datalevel[$drafter][$level][$status] = 0;
            }

            $datalevel[$drafter][$level][$status]++;
            $datastatus[$drafter]["Working Progress"]++;

            if ($status == "RELEASED") {
                $datastatus[$drafter]["RELEASED"]++;
                $datastatus[$drafter]["Working Progress"]--;
            } elseif ($status != "RELEASED" && $status != "Working Progress") {
                $datastatus[$drafter]["Belum Dimulai"]++;
                $datastatus[$drafter]["Working Progress"]--;
            }

            if (!isset($datalevel['all'][$level])) {
                $datalevel['all'][$level] = ['RELEASED' => 0, 'Working Progress' => 0, 'Belum Dimulai' => 0];
            }
            if (!isset($datalevel['all'][$level][$status])) {
                $datalevel['all'][$level][$status] = 0;
            }

            $datalevel['all'][$level][$status]++;
            $datastatus['all']["Working Progress"]++;

            if ($status == "RELEASED") {
                $datastatus['all']["RELEASED"]++;
                $datastatus['all']["Working Progress"]--;
            } elseif ($status != "RELEASED" && $status != "Working Progress") {
                $datastatus['all']["Belum Dimulai"]++;
                $datastatus['all']["Working Progress"]--;
            }
        }

        return ['datalevel' => $datalevel, 'datastatus' => $datastatus];
    }

    public function closepercentage($progressReports)
    {
        $revisi = $progressReports;
        $documentrelease = 0;
        foreach ($revisi as $index => $item) {
            $statuscondition = $item['status'];
            if ($statuscondition == "RELEASED") {
                $documentrelease += 1;
            }
        }
        if (count($revisi) != 0) {
            $seniorpercentage = $documentrelease / count($revisi) * 100;
        } else {
            $seniorpercentage = 0;
        }
        return $seniorpercentage;
    }
    public function calculatePercentageData($datalevel, $datastatus)
    {
        $percentageLevel = [];
        $percentageStatus = [];

        foreach ($datalevel as $drafter => $levels) {
            foreach ($levels as $level => $statuses) {
                $total = array_sum($statuses);
                $percentageLevel[$drafter][$level] = array_map(function ($count) use ($total) {
                    return $total ? ($count / $total) * 100 : 0;
                }, $statuses);
            }

            $totalStatus = array_sum($datastatus[$drafter]);
            $percentageStatus[$drafter] = $totalStatus ? array_map(function ($count) use ($totalStatus) {
                return ($count / $totalStatus) * 100;
            }, $datastatus[$drafter]) : $datastatus[$drafter];
        }

        return ['percentageLevel' => $percentageLevel, 'percentageStatus' => $percentageStatus];
    }

    public function calculateWeeklyData($revisi, $startproject, $endproject)
    {
        $documentrelease = 0;
        foreach ($revisi as $index => $item) {
            $statuscondition = $item['status'];
            if ($statuscondition == "RELEASED") {
                $documentrelease += 1;
            }
        }

        $seniorpercentage = count($revisi) != 0 ? $documentrelease / count($revisi) * 100 : 0;

        $data = [];
        $starttime = new DateTime($startproject);
        $endtime = clone $starttime;
        $endtime->modify('+6 days');

        while ($endtime <= new DateTime($endproject)) {
            $nameofweek = $starttime->format('d-m-Y') . " - " . $endtime->format('d-m-Y');

            $data[$nameofweek] = [
                'start' => $starttime->format('d-m-Y'),
                'end' => $endtime->format('d-m-Y'),
                'nilai' => 0,
                'nilaipresentase' => 0
            ];

            $starttime->modify('+7 days');
            $endtime->modify('+7 days');
        }

        foreach ($revisi as $itemrevisi) {
            $tanggalRealisasi = $itemrevisi['realisasi'];

            if (!empty($tanggalRealisasi)) {
                $date = DateTime::createFromFormat('d-m-Y', $tanggalRealisasi);
                if ($date) {
                    foreach ($data as $index => $item) {
                        $dateawal = DateTime::createFromFormat('d-m-Y', $item['start']);
                        $dateakhir = DateTime::createFromFormat('d-m-Y', $item['end']);

                        if ($dateawal && $dateakhir && $date >= $dateawal && $date <= $dateakhir && $itemrevisi['status'] == "RELEASED") {
                            $data[$index]['nilai'] += 1;
                        }
                    }
                }
            }
        }

        $totaldata = 0;
        foreach ($data as $nameofweek => $item) {
            $totaldata += $item['nilai'];

            if (count($revisi) != 0) {
                $data[$nameofweek]['nilaipresentase'] = $totaldata / count($revisi) * 100;
            } else {
                $data[$nameofweek]['nilaipresentase'] = 0;
            }
        }

        return $data;
    }

    public function calculateWeeklyPercentage($revisi, $startproject, $endproject)
    {
        $weekData = [];
        $weekStart = new DateTime($startproject);
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');

        while ($weekEnd <= new DateTime($endproject)) {
            $weekName = $weekStart->format('d-m-Y') . " - " . $weekEnd->format('d-m-Y');

            $weekData[$weekName] = [
                'start' => $weekStart->format('d-m-Y'),
                'end' => $weekEnd->format('d-m-Y'),
                'value' => 0,
                'percentage' => 0
            ];

            $weekStart->modify('+7 days');
            $weekEnd->modify('+7 days');
        }

        foreach ($revisi as $revisionItem) {
            $realizationDate = $revisionItem['deadlinerelease'];

            if (!empty($realizationDate)) {
                $revisionDate = DateTime::createFromFormat('d-m-Y', $realizationDate);
                if ($revisionDate) {
                    foreach ($weekData as $weekIndex => $weekItem) {
                        $weekStartDate = DateTime::createFromFormat('d-m-Y', $weekItem['start']);
                        $weekEndDate = DateTime::createFromFormat('d-m-Y', $weekItem['end']);

                        if ($weekStartDate && $weekEndDate && $revisionDate >= $weekStartDate && $revisionDate <= $weekEndDate && $revisionItem['status'] == "RELEASED") {
                            $weekData[$weekIndex]['value'] += 1;
                        }
                    }
                }
            }
        }

        $totalRevisions = count($revisi);
        foreach ($weekData as $weekName => &$weekItem) {
            $weekItem['percentage'] = $totalRevisions != 0 ? ($weekItem['value'] / $totalRevisions) * 100 : 0;
        }
        unset($weekItem); // Break reference to last item

        return $weekData;
    }

    public function getProgressData()
    {
        $progressReports = $this->newprogressreports;
        $indukan = [];
        $listprogressnodokumen = [];

        foreach ($progressReports as $progressReport) {
            $listprogressnodokumen[] = $progressReport->nodokumen ?? '';

            if (isset($progressReport->parent_revision_id)) {
                $parentId = strval($progressReport->parent_revision_id);
                $status = $progressReport->status ?? '';
                $countrelease = ($status === "RELEASED") ? 1 : 0;

                if (!isset($indukan[$parentId])) {
                    $indukan[$parentId] = [
                        "dokumen" => [],
                        "persen" => [
                            'count' => 1,
                            'countrelease' => $countrelease
                        ]
                    ];
                } else {
                    $indukan[$parentId]["persen"]['count'] += 1;
                    $indukan[$parentId]["persen"]['countrelease'] += $countrelease;
                }

                $indukan[$parentId]["dokumen"][] = [
                    'id' => $progressReport->id ?? '',
                    'namadokumen' => $progressReport->namadokumen ?? '',
                    'nodokumen' => $progressReport->nodokumen ?? '',
                    'status' => $progressReport->status ?? '',
                ];
            }
        }

        return [
            'indukan' => $indukan,
            'listprogressnodokumen' => json_encode($listprogressnodokumen),
            'progressReports' => $progressReports
        ];
    }

    public function doubledetector()
    {
        $progressReports = $this->newprogressreports;
        $nodokumenCount = [];
        $duplicates = [];

        // Hitung kemunculan setiap nodokumen
        foreach ($progressReports as $progressReport) {
            $nodokumen = trim($progressReport->nodokumen); // Trimming to remove any extra spaces

            if (isset($nodokumenCount[$nodokumen])) {
                $nodokumenCount[$nodokumen]++;
            } else {
                $nodokumenCount[$nodokumen] = 1;
            }
        }

        // Cari nodokumen yang kembar
        foreach ($nodokumenCount as $nodokumen => $count) {
            if ($count > 1) {
                $duplicates[] = $nodokumen;
            }
        }



        // Kembalikan hasil sebagai JSON
        if (!empty($duplicates)) {
            return $duplicates;
        } else {
            return [];
        }
    }

    public function doubledetectorcount()
    {
        $duplicates = $this->doubledetector();
        $countduplicates = count($duplicates);
        return $countduplicates;
    }

    public function destroydian()
    {
        $progressReports = $this->newprogressreports;
        $projectandvalue = Newreport::calculatelastpercentage();
        $this->systemLogs()->create([
            'message' => json_encode([
                'message' => 'Data dihapus',
                'datasebelum' => $progressReports,
                'datasesudah' => [],
                'persentase' => $projectandvalue[0],
                'persentase_internal' => $projectandvalue[1],
            ]),
            'level' => 'info',
            'user' => auth()->user()->name,
            'user_id' => auth()->user()->id, // Add user_id here
            'aksi' => 'progressdelete',
        ]);
        foreach ($progressReports as $progressReport) {
            $status = $progressReport->status;
            if ($status != "RELEASED") {
                $progressReport->delete(); // Correct the deletion syntax
            }
        }

        return "Sukses";
    }

    public static function percentageandcount($listproject, $revisiall)
    {
        // Eager load the progress reports and their related progress data
        $progressreports = Newreport::with('newprogressreports')->orderBy('created_at', 'desc')->get();

        foreach ($listproject as $keyan) {
            // Filter progress reports based on project type
            $filteredprogressreports = $keyan != "All"
                ? $progressreports->where('proyek_type', $keyan)
                : $progressreports;

            $documentrelease = 0;
            $documentunrelease = 0;

            // Calculate the number of released and unreleased documents
            foreach ($filteredprogressreports as $newreport) {
                $progressData = $newreport->getProgressData();
                $progressReports = $progressData['progressReports'];

                foreach ($progressReports as $item) {
                    $statuscondition = $item->status;
                    if ($statuscondition == "RELEASED") {
                        $documentrelease++;
                    } else {
                        $documentunrelease++;
                    }
                }
            }

            $totaldocument = $documentrelease + $documentunrelease;
            $positifnewreport = $totaldocument > 0 ? ($documentrelease / $totaldocument * 100) : 0;

            $revisiall[$keyan] = [
                'persentaseprogressreport' => [
                    'terselesaikan' => $positifnewreport,
                    'tidak terselesaikan' => 100 - $positifnewreport
                ],
                'jumlahprogressreport' => [
                    'terselesaikan' => $documentrelease,
                    'tidak terselesaikan' => $documentunrelease
                ]
            ];
        }

        return $revisiall;
    }

    public static function indexnewreport($unitsingkatan, $listproject)
    {
        $userdef = auth()->user();
        /////////// KHUSUS KCI ////////////////////
        $unitvalue = [];
        $unitvalue['Desain Bogie & Wagon'] = 158;
        $unitvalue['Sistem Mekanik & Interior'] = 2485;
        $unitvalue['Desain Elektrik'] = 232;
        $unitvalue['Desain Mekanik'] = 0;
        $unitvalue['Desain Mekanik & Interior'] = 0;
        $unitvalue['Sistem Mekanik'] = 542;
        $unitvalue['Desain Interior'] = 1943;
        $unitvalue['Welding Technology'] = 179;
        $unitvalue['Shop Drawing'] = 89;
        $unitvalue['Preparation & Support'] = 57;
        $unitvalue['Teknologi Proses'] = 534;
        $unitvalue['Mechanical Engineering System'] = 38;
        $unitvalue['Desain Carbody'] = 229;
        $unitvalue['Quality Engineering'] = 194;
        $unitvalue['Product Engineering'] = 9;
        $unitvalue['Electrical Engineering System'] = 52;
        $unitvalue['MTPR'] = 1;
        /////////// KHUSUS KCI ////////////////////

        $newreports = Newreport::with('newprogressreports')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($newreports as $newreport) {
            $progressData = $newreport->getProgressData();
            $newreport->progressReports = $progressData['progressReports'];
            $progressReports = $newreport->progressReports;
            $documentrelease = 0;
            $revisi = $progressReports;
            foreach ($revisi as $index => $item) {
                $statuscondition = $item['status'];
                if ($statuscondition == "RELEASED") {
                    $documentrelease += 1;
                }
            }
            if (count($revisi) != 0) {
                $seniorpercentage = $documentrelease / count($revisi) * 100;
            } else {
                $seniorpercentage = 0;
            }
            $newreport->seniorpercentage = $seniorpercentage;
            $newreport->documentcount = count($revisi);

            //countrelease by external internal start
            $releaseinfo = $newreport->releasecount();

            if (!in_array($userdef->rule, ['QC FAB', 'QC FIN', 'QC FAB', 'QC FIN', 'QC INC', 'Fabrikasi', 'PPC', 'QC Banyuwangi', 'Pabrik Banyuwangi', 'Fabrikasi', 'PPC'])) {

                // sesi internal menjadi back background dan eksternal menjadi main background
                if (session('internalon')) {
                    $newreport->release = $releaseinfo['countrelease'];
                } else {
                    if (($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")) {
                        $newreport->release = $releaseinfo['countrelease'] + $releaseinfo['countunrelease'];
                    } else {
                        $newreport->release = $releaseinfo['countrelease'];
                    }

                    ///////////////////////// KCI ////////////////////////
                    if ($newreport->proyek_type == "KCI" && ($newreport->unit != null || $newreport->unit != "")) {
                        $newreport->seniorpercentage = 100;
                        $newreport->documentcount = $unitvalue[$newreport->unit];
                        $newreport->releasecount = $unitvalue[$newreport->unit];
                        $newreport->release = $unitvalue[$newreport->unit];
                    }
                    ///////////////////////// KCI ////////////////////////

                }



            } else {

                // sesi internal menjadi main background dan eksternal menjadi back background
                if (!session('internalon')) {
                    $newreport->release = $releaseinfo['countrelease'];
                } else {
                    if (($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") || ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")) {
                        $newreport->release = $releaseinfo['countrelease'] + $releaseinfo['countunrelease'];
                    } else {
                        $newreport->release = $releaseinfo['countrelease'];
                    }

                    ///////////////////////// KCI ////////////////////////
                    if ($newreport->proyek_type == "KCI" && ($newreport->unit != null || $newreport->unit != "")) {
                        $newreport->seniorpercentage = 100;
                        $newreport->documentcount = $unitvalue[$newreport->unit];
                        $newreport->releasecount = $unitvalue[$newreport->unit];
                        $newreport->release = $unitvalue[$newreport->unit];
                    }
                    ///////////////////////// KCI ////////////////////////

                }

            }


        }
        $revisiall = [];
        for ($i = 0; $i < count($listproject); $i++) {
            $key = str_replace(' ', '_', $listproject[$i]->title);
            $revisiall[$key]['newreports'] = collect($newreports)->where('proyek_type_id', $listproject[$i]->id)->all();
        }
        return [$newreports, $revisiall];
    }

    public static function indexnewreportkci($unitsingkatan, $listproject)
    {
        $unitvalue = [];
        $unitvalue['Desain Bogie & Wagon'] = 158;
        $unitvalue['Sistem Mekanik & Interior'] = 2485;
        $unitvalue['Desain Elektrik'] = 232;
        $unitvalue['Desain Mekanik'] = 0;
        $unitvalue['Desain Mekanik & Interior'] = 0;
        $unitvalue['Sistem Mekanik'] = 542;
        $unitvalue['Desain Interior'] = 1943;
        $unitvalue['Welding Technology'] = 179;
        $unitvalue['Shop Drawing'] = 89;
        $unitvalue['Preparation & Support'] = 57;
        $unitvalue['Teknologi Proses'] = 534;
        $unitvalue['Mechanical Engineering System'] = 38;
        $unitvalue['Desain Carbody'] = 229;
        $unitvalue['Quality Engineering'] = 194;
        $unitvalue['Product Engineering'] = 9;
        $unitvalue['Electrical Engineering System'] = 52;

        $newreports = Newreport::with('newprogressreports')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($newreports as $newreport) {
            $progressData = $newreport->getProgressData();
            $newreport->progressReports = $progressData['progressReports'];
            $progressReports = $newreport->progressReports;
            $documentrelease = 0;
            $revisi = $progressReports;
            foreach ($revisi as $index => $item) {
                $statuscondition = $item['status'];
                if ($statuscondition == "RELEASED") {
                    $documentrelease += 1;
                }
            }
            if (count($revisi) != 0) {
                $seniorpercentage = $documentrelease / count($revisi) * 100;
            } else {
                $seniorpercentage = 0;
            }


            $newreport->seniorpercentage = 100;
            $newreport->documentcount = $unitvalue[$newreport->unit];
            $newreport->releasecount = $unitvalue[$newreport->unit];
            $newreport->release = $unitvalue[$newreport->unit];
        }
        $revisiall = [];
        for ($i = 0; $i < count($listproject); $i++) {
            $key = str_replace(' ', '_', $listproject[$i]);
            $revisiall[$key]['newreports'] = collect($newreports)->where('proyek_type_id', 1)->all();
        }
        return [$newreports, $revisiall];
    }


    public static function byprojectprogress($unitsingkatan, $listproject, $startDate, $endDate)
    {
        // Fetch all new reports with their progress reports filtered by 'proyek_type'
        $newreports = Newreport::with('newprogressreports')->whereIn('proyek_type', $listproject)->orderBy('created_at', 'desc')->get();

        // Convert $startDate and $endDate to Carbon instances
        $startDate = \Carbon\Carbon::parse($startDate);
        $endDate = \Carbon\Carbon::parse($endDate);

        foreach ($newreports as $newreport) {
            $progressData = $newreport->newprogressreports;
            $documentrelease = 0;
            $satuini = [];
            foreach ($progressData as $item) {
                if (stripos($item->status, "RELEASED") !== false) {
                    // Parse the 'realisasi' date in 'd-m-Y' format
                    if (\Carbon\Carbon::hasFormat(trim($item->realisasi), 'd-m-Y')) {
                        $realisasiDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($item->realisasi));
                        if ($realisasiDate->between($startDate, $endDate)) {
                            $documentrelease++;
                        } else {
                            $satuini[] = $item->nodokumen;
                        }
                    } else {
                        $documentrelease++;
                    }
                }



            }
            $newreport->satuini = json_encode($satuini);
            $totalReports = count($progressData);
            $seniorpercentage = $totalReports > 0 ? ($documentrelease / $totalReports) * 100 : 0;

            $newreport->seniorpercentage = $seniorpercentage;

            // Set external and internal percentages
            $isKCIOrTB1014 = ($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") ||
                ($newreport->unit == "Sistem Mekanik" && $newreport->proyek_type == "KCI") ||
                ($newreport->unit == "Desain Interior" && $newreport->proyek_type == "KCI") ||
                ($newreport->unit == "Desain Carbody" && $newreport->proyek_type == "KCI") ||
                ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014");

            $newreport->totalpersentaseeksternal = $isKCIOrTB1014 ? 100.00 : number_format($seniorpercentage, 2);
            $newreport->totalpersentaseinternal = number_format($seniorpercentage, 2);
            $newreport->documentcount = $totalReports;
            $newreport->documentrelease = $documentrelease;
        }

        $revisiall = [];
        foreach ($listproject as $project) {
            $key = str_replace(' ', '_', $project);
            $revisiall[$key]['newreports'] = collect($newreports)->where('proyek_type', $project)->all();
        }

        return [$newreports, $revisiall];
    }


    public static function downloadprogressbyproject($project, $startDate, $endDate)
    {
        $newreports = Newreport::where('proyek_type', $project)->get();

        // Data to be exported
        $informasi = [];

        // Loop through each Newreport to gather progress data and calculate weekly data
        foreach ($newreports as $newreport) {
            $unit = $newreport->unit;
            $project = $newreport->proyek_type;

            // Fetch progress data from Newreport
            $progressData = $newreport->getProgressData();

            // Calculate weekly data based on the selected date range
            $data = $newreport->calculateWeeklyData($progressData['progressReports'], $startDate, $endDate);
            $weekData = $newreport->calculateWeeklyPercentage($progressData['progressReports'], $startDate, $endDate);

            // Combine data for export
            $exportData = [];
            foreach ($data as $week => $item) {
                $exportData[] = [
                    'Week' => $week,
                    'Start Date' => $item['start'],
                    'End Date' => $item['end'],
                    'Total Revisions (Plan)' => $weekData[$week]['value'],
                    'Total Revisions (Realisasi)' => $item['nilai'],
                    'Total Percentage (Plan)' => $weekData[$week]['percentage'],
                    'Total Percentage (Realisasi)' => $item['nilaipresentase'],
                ];
            }

            $informasi[] = [
                'Unit' => $unit,
                'Project' => $project,
                'Exporteddata' => $exportData,
            ];
        }
        return $informasi;
    }

    public static function downloaddoubledetector($project)
    {
        $newreports = Newreport::where('proyek_type', $project)->get();
        // Data to be exported
        $informasi = [];
        // Loop through each Newreport to gather progress data and calculate weekly data
        foreach ($newreports as $newreport) {
            $unit = $newreport->unit;
            $project = $newreport->proyek_type;

            // Fetch progress data from Newreport
            $duplicate = $newreport->doubledetector();

            // Convert the duplicate list to a comma-separated string
            $duplicateString = implode(',', $duplicate);

            $informasi[] = [
                'Unit' => $unit,
                'Project' => $project,
                'Duplicate' => $duplicate,
            ];
        }

        return $informasi;
    }

    public function releasecount()
    {
        $progressReports = $this->newprogressreports;
        $countrelease = 0;
        $countunrelease = 0;

        foreach ($progressReports as $progressReport) {
            if ($progressReport->status == "RELEASED") {
                $countrelease++;
            } else {
                $countunrelease++;
            }
        }

        $total = count($progressReports);
        if ($total != 0) {
            $progresspercentage = ($countrelease / $total) * 100;
        } else {
            $progresspercentage = 0;
        }
        $data = [
            'countrelease' => $countrelease,
            'countunrelease' => $countunrelease,
            'progresspercentage' => $progresspercentage
        ];
        return $data;
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

    public static function calculatelastpercentage()
    {

        $projects = ProjectType::all();
        $allunitunderpe = Unit::all()->pluck('title')->toArray();

        // Buat array untuk menyimpan singkatan unit
        $unitsingkatan = [];
        foreach ($allunitunderpe as $unit) {
            $unitsingkatan[$unit] = self::singkatanUnit($unit);
        }
        [$newreports, $revisiall] = self::indexnewreport($unitsingkatan, $projects);
        $projectandvalue_external = [];
        $projectandvalue_internal = [];
        foreach ($revisiall as $keyan => $revisi) {
            if ($keyan !== 'All') {
                $newreports = $revisi['newreports'];
                $totalpersentaseeksternalall = 0;
                $totalpersentaseinternalall = 0;
                $totaldocument = 0;
                $totalunit = 0;
                foreach ($newreports as $newreport) {
                    $totalunit += 1;
                }

            }

            foreach ($newreports as $newreport) {
                if ($newreport->unit == "Sistem Mekanik" || $newreport->unit == "Desain Interior" || $newreport->unit == "Desain Carbody" || ($newreport->unit == "Product Engineering" && $newreport->proyek_type == "100 Unit Bogie TB1014")) {
                    $totalpersentaseeksternal = 100 / $totalunit;
                } elseif ($newreport->unit == "Desain Bogie & Wagon" && $newreport->proyek_type == "KCI") {
                    $totalpersentaseeksternal = 100 / $totalunit;
                } else {
                    $totalpersentaseeksternal = number_format($newreport->seniorpercentage, 2) / $totalunit;
                }
                $totalpersentaseinternal = number_format($newreport->seniorpercentage, 2) / $totalunit;

                $totalpersentaseeksternalall += $totalpersentaseeksternal;
                $totalpersentaseinternalall += $totalpersentaseinternal;
                $totaldocument += $newreport->documentcount;
            }
            $totalpersentaseeksternalall = number_format($totalpersentaseeksternalall, 2);
            $totalpersentaseinternalall = number_format($totalpersentaseinternalall, 2);
            $projectandvalue_external[$keyan] = $totalpersentaseeksternalall;
            $projectandvalue_internal[$keyan] = $totalpersentaseinternalall;
        }
        return [$projectandvalue_external, $projectandvalue_internal];
    }

    public static function historyPercentage()
    {
        // Retrieve all logs related to Newreport
        $logs = SystemLog::where('loggable_type', 'App\Models\Newreport')->get();
        return $logs;
    }

}
