<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Newprogressreport extends Model
{
    protected $fillable = [
        'drafter_id',
        'temporystatus',
        'parent_revision_id',
        'dcr',
        'startreleasedate',
        'deadlinereleasedate',
        'documentkind_id ',
        'level_id',
        'papersize',
        'sheet',
        'releasedagain'
    ]; // Specify the fields that are mass assignable

    public function newbomkomats()
    {
        return $this->belongsToMany(Newbomkomat::class, 'newbomkomat_newprogressreport', 'newprogressreport_id', 'newbomkomat_id')
            ->withTimestamps();
    }

    public function newprogressreporthistory()
    {
        return $this->hasMany(Newprogressreporthistory::class, 'newprogressreport_id');
    }
    // Relasi ke dirinya sendiri untuk parent
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_revision_id');
    }

    // Relasi ke dirinya sendiri untuk children
    public function children()
    {
        return $this->hasMany(self::class, 'parent_revision_id');
    }

    public function newreport()
    {
        return $this->belongsTo(Newreport::class);
    }

    public function documentKind()
    {
        return $this->belongsTo(NewProgressReportDocumentKind::class, 'documentkind_id');
    }



    public function revisions()
    {
        return $this->hasMany(Revision::class);
    }

    public function levelKind()
    {
        return $this->belongsTo(NewProgressReportsLevel::class, 'level_id');
    }

    public function jobticketHistories()
    {
        return $this->hasMany(JobticketHistory::class, 'newprogressreport_id');
    }

    // Start a new task
    public function starttugasbaru()
    {
        $datawaktu = json_decode($this->temporystatus, true) ?? [];
        $datawaktu['start_time_awal'] = Carbon::now();
        $datawaktu['start_time'] = Carbon::now();
        $datawaktu['pause_time'] = null;
        $datawaktu['total_elapsed_seconds'] = 0;
        $datawaktu['statusrevisi'] = 'ditutup';
        $this->temporystatus = json_encode($datawaktu);
        $this->save();
    }

    // Pause the current task
    public function pausetugasbaru()
    {
        $datawaktu = json_decode($this->temporystatus, true) ?? [];
        if (isset($datawaktu['start_time'])) {
            $now = Carbon::now();
            $elapsed = $now->diffInSeconds($datawaktu['start_time']);
            $datawaktu['pause_time'] = $now;
            $datawaktu['total_elapsed_seconds'] += $elapsed;
        }
        $this->temporystatus = json_encode($datawaktu);
        $this->save();

        return true;
    }


    public function resumetugasbaru()
    {
        $datawaktu = json_decode($this->temporystatus, true) ?? [];
        if (isset($datawaktu['pause_time'])) {
            $datawaktu['start_time'] = Carbon::now();
            $datawaktu['pause_time'] = null;
        }
        $this->temporystatus = json_encode($datawaktu);
        $this->save();
        $pauseTime = new Carbon($datawaktu['pause_time']);
        $currentElapsedSeconds = $datawaktu['total_elapsed_seconds'] + $pauseTime->diffInSeconds(Carbon::now());

        return [
            'startTime' => Carbon::now(),
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $currentElapsedSeconds
        ];
    }

    // Reset the current task
    public function resettugasbaru()
    {
        $datawaktu = [];

        $this->drafter = null;
        $this->temporystatus = json_encode($datawaktu);
        $this->revisions()->delete();
        $this->save();

        return true;
    }

    // Finish the current task and create a new revision
    public function selesaitugasbaru()
    {
        $revisionCount = $this->revisions()->count();
        $nextRevisionNumber = $revisionCount;
        $revisionName = $this->convertToAlphabetic($nextRevisionNumber);

        $temporystatus = json_decode($this->temporystatus, true) ?? [];
        $startTime = $temporystatus['start_time_awal'];
        $totalElapsedSeconds = $temporystatus['total_elapsed_seconds'] ?? 0;
        if (isset($temporystatus['pause_time'])) {
            $pauseTime = new Carbon($temporystatus['pause_time']);
            $totalElapsedSeconds += $pauseTime->diffInSeconds(Carbon::now());
        }

        $newRevisionData = [
            'revisionname' => $revisionName,
            'end_time_run' => Carbon::now(),
            'revision_status' => "belum divalidasi",
            'total_elapsed_seconds' => $totalElapsedSeconds,
        ];

        $newRevision = $this->revisions()->create($newRevisionData);

        $temporystatus = [
            'start_time' => null,
            'pause_time' => null,
            'total_elapsed_seconds' => 0,
            'statusrevisi' => "ditutup",
            'revisionlast' => $revisionName,
        ];
        $this->temporystatus = json_encode($temporystatus);
        $this->save();

        return [
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $temporystatus['total_elapsed_seconds'],
            'lastKey' => $revisionName,
        ];
    }

    public function histories()
    {
        return $this->hasMany(Newprogressreporthistory::class, 'newprogressreport_id');
    }

    public function getLatestRevAttribute()
    {
        $histories = collect($this->histories);

        // Sorting berdasarkan 'rev' secara descending
        $sortedHistories = $histories->sortByDesc('rev');

        // Ambil elemen pertama setelah sorting, jika ada
        $firstHistory = $sortedHistories->first();

        return $firstHistory ? $firstHistory : null;
    }

    // Helper method to convert a number to an alphabetic string (e.g., 1 -> A, 2 -> B, ...)
    protected function convertToAlphabetic($number)
    {
        if ($number == 0) {
            return '0';
        }

        $alphabet = range('A', 'Z');
        $result = '';

        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $result = $alphabet[$remainder] . $result;
            $number = (int) (($number - $remainder) / 26);
        }

        return $result;
    }



    public function izinkanrevisitugasbaru()
    {

        $datawaktu = json_decode($this->temporystatus, true) ?? [];
        $datawaktu['statusrevisi'] = "dibuka";
        $this->temporystatus = json_encode($datawaktu);
        $this->save();
        $pauseTime = new Carbon($datawaktu['pause_time']);
        $currentElapsedSeconds = $datawaktu['total_elapsed_seconds'] + $pauseTime->diffInSeconds(Carbon::now());
        return [
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $currentElapsedSeconds
        ];
    }




    public static function getProjectDatastatisfordashboard($project)
    {

        $availableDocumentNames = Cache::remember('available_document_names', 8 * 60 * 60, function () {
            return NewProgressReportDocumentKind::pluck('name', 'id');
        });

        $levels = Cache::remember('new_progress_reports_levels', 8 * 60 * 60, function () {
            return NewProgressReportsLevel::pluck('title', 'id');
        });

        $projectType = Cache::remember("project_type_{$project}", 8 * 60 * 60, function () use ($project) {
            return ProjectType::where('title', $project)->first();
        });


        $projectData = []; // Data proyek untuk response JSON
        $leveldocuments = [];
        $kinddocuments = [];

        // Level 1 - Divisi
        $technology = [
            'id' => '1',
            'name' => 'TECHNOLOGY',
            // 'collapsed' => true,
            'Releasedcount' => 0,
            'Unreleasedcount' => 0,
            'fontSymbol' => 'smile-o',
        ];

        $units = [];


        $newReports = $projectType->newreports; // Menggunakan relasi

        $newReportIds = $newReports->pluck('id')->toArray();

        $key = 'new_progress_reports_' . md5(implode('_', $newReportIds));

        $newProgressReportsall = Cache::remember($key, 8 * 60 * 60, function () use ($newReportIds) {
            return NewProgressReport::with(['newreport', 'histories'])
                ->whereIn('newreport_id', $newReportIds)
                ->get();
        });


        // Proses koleksi untuk memfilter berdasarkan kondisi whereNotNull
        $newProgressReports = $newProgressReportsall->filter(function ($item) {
            return !is_null($item->documentkind_id) &&
                !is_null($item->level_id) &&
                !is_null($item->startreleasedate) &&
                !is_null($item->deadlinereleasedate);
        });


        $documentKeys = [];


        foreach ($newProgressReports as $newProgressReport) {
            $unitKey = 'unit' . $newProgressReport['newreport_id'];
            $units[$unitKey] = [
                'id' => 'unit' . $newProgressReport['newreport_id'],
                'name' => $newProgressReport->newreport->unit,
                'collapsed' => true,
                'parent' => '1',
                'Releasedcount' => 0,
                'Unreleasedcount' => 0,
            ];
        }

        foreach ($newProgressReports as $newProgressReport) {
            // Gabungkan ketiga variabel menjadi satu kunci unik
            $key = $newProgressReport['documentkind_id'] . '-' . $newProgressReport['newreport_id'] . '-' . $newProgressReport['level_id'];


            // Simpan data berdasarkan kunci unik
            $startDate = $newProgressReport['startreleasedate'];
            $deadlineDate = $newProgressReport['deadlinereleasedate'];

            // Parse tanggal agar dapat dibandingkan dengan benar
            $startDateTimestamp = strtotime($startDate);
            $deadlineDateTimestamp = strtotime($deadlineDate);

            // Jika kunci belum ada, inisialisasi
            if (!isset($documentKeys[$key])) {
                $documentKeys[$key] = [
                    'unit' => $newProgressReport->newreport->unit,
                    'documentkind_id' => $newProgressReport['documentkind_id'],
                    'newreport_id' => $newProgressReport['newreport_id'],
                    'level_id' => $newProgressReport['level_id'],
                    'start' => date('Y-m-d H:i:s', $startDateTimestamp),
                    'end' => date('Y-m-d H:i:s', $deadlineDateTimestamp),
                ];
            } else {
                // Selalu update tanggal mulai jika tanggal baru lebih awal
                if ($startDateTimestamp <= strtotime($documentKeys[$key]['start'])) {
                    $documentKeys[$key]['start'] = date('Y-m-d H:i:s', $startDateTimestamp);
                }

                // Selalu update tanggal selesai jika tanggal baru lebih akhir
                if ($deadlineDateTimestamp >= strtotime($documentKeys[$key]['end'])) {
                    $documentKeys[$key]['end'] = date('Y-m-d H:i:s', $deadlineDateTimestamp);
                }
            }


            // Update technology start and end
            if (!isset($technology['start']) || empty($technology['start'])) {
                $technology['start'] = date('Y-m-d H:i:s', $startDateTimestamp);
            } else {
                if ($startDateTimestamp < strtotime($technology['start'])) {
                    $technology['start'] = date('Y-m-d H:i:s', $startDateTimestamp);
                }
            }

            if (!isset($technology['end']) || empty($technology['end'])) {
                $technology['end'] = date('Y-m-d H:i:s', $deadlineDateTimestamp);
            } else {
                if ($deadlineDateTimestamp > strtotime($technology['end'])) {
                    $technology['end'] = date('Y-m-d H:i:s', $deadlineDateTimestamp);
                }
            }


            $unitKey = 'unit' . $newProgressReport['newreport_id'];


            // Update $units['unit' . $newProgressReport['newreport_id']] start and end
            // Update tanggal mulai dan selesai untuk unit terkait
            if (!empty($newProgressReport['startreleasedate'])) {
                $startDate = strtotime($newProgressReport['startreleasedate']);
                if (empty($units[$unitKey]['start']) || $startDate < strtotime($units[$unitKey]['start'])) {
                    $units[$unitKey]['start'] = date('Y-m-d H:i:s', $startDate);
                }
            }

            if (!empty($newProgressReport['deadlinereleasedate'])) {
                $deadlineDate = strtotime($newProgressReport['deadlinereleasedate']);
                if (empty($units[$unitKey]['end']) || $deadlineDate > strtotime($units[$unitKey]['end'])) {
                    $units[$unitKey]['end'] = date('Y-m-d H:i:s', $deadlineDate);
                }
            }


        }


        $uniqueDocuments = $documentKeys;


        foreach ($uniqueDocuments as $uniqueKey => $document) {
            $levelKey = $document['level_id'] . '-' . $document['newreport_id'];
            if (!isset($leveldocuments[$levelKey])) {
                $leveldocuments[$levelKey] = [
                    'id' => $levelKey,
                    'name' => $levels[$document['level_id']],
                    'collapsed' => true,
                    'parent' => 'unit' . $document['newreport_id'],
                    'Releasedcount' => 0,
                    'Unreleasedcount' => 0,
                ];

                $startDate = strtotime($document['start']);
                if (empty($leveldocuments[$levelKey]['start']) || $startDate < strtotime($leveldocuments[$levelKey]['start'])) {
                    $leveldocuments[$levelKey]['start'] = date('Y-m-d H:i:s', $startDate);
                }

                $deadlineDate = strtotime($document['end']);
                if (empty($leveldocuments[$levelKey]['end']) || $deadlineDate > strtotime($leveldocuments[$levelKey]['end'])) {
                    $leveldocuments[$levelKey]['end'] = date('Y-m-d H:i:s', $deadlineDate);
                }
            }



            $kindKey = $document['documentkind_id'] . '-' . $document['newreport_id'] . '-' . $document['level_id'];

            if (!isset($kinddocuments[$kindKey])) {
                $kinddocuments[$kindKey] = [
                    'id' => $kindKey,
                    'name' => $availableDocumentNames[$document['documentkind_id']] . " (" . $levels[$document['level_id']] . ")",
                    'start' => self::parseDate($document['start']),
                    'end' => self::parseDate($document['end']),
                    'collapsed' => true,
                    'parent' => $levelKey,
                    'Releasedcount' => 0,
                    'Unreleasedcount' => 0,
                ];
            }
        }




        foreach ($newProgressReports as $newProgressReport) {

            $data = [
                'id' => $newProgressReport['id'],
                'name' => $newProgressReport['nodokumen'] . "-" . $newProgressReport['namadokumen'],
                'collapsed' => true,
                'parent' => $newProgressReport['documentkind_id'] . '-' . $newProgressReport['newreport_id'] . '-' . $newProgressReport['level_id'],
                'completed' => self::calculateCompletionPercentage($newProgressReport['status'] === 'RELEASED' ? 1 : 0, 0),
                'color' => self::setColor($newProgressReport['status'] === 'RELEASED' ? 1 : 0, 0, 'plan'),

            ];
            if (!empty($newProgressReport['startreleasedate'])) {
                $startDate = self::parseDate($newProgressReport['startreleasedate']);
                $data['start'] = $startDate;
            }

            if (!empty($newProgressReport['deadlinereleasedate'])) {
                $deadlineDate = self::parseDate($newProgressReport['deadlinereleasedate']);
                $data['end'] = $deadlineDate;
            }


            if ($project != 'KCIA') {
                $projectData[] = $data;
                // Level 5 - Detail Dokumen
            }



            $unitKey = 'unit' . $newProgressReport['newreport_id'];
            $levelKey = $newProgressReport['level_id'] . '-' . $newProgressReport['newreport_id'];
            $kindKey = $newProgressReport['documentkind_id'] . '-' . $newProgressReport['newreport_id'] . '-' . $newProgressReport['level_id'];

            if ($newProgressReport['status'] == "RELEASED") {
                $technology['Releasedcount'] += 1;
                $units[$unitKey]['Releasedcount'] += 1;
                $leveldocuments[$levelKey]['Releasedcount'] += 1;
                $kinddocuments[$kindKey]['Releasedcount'] += 1;






            } else {
                $technology['Unreleasedcount'] += 1;
                $units[$unitKey]['Unreleasedcount'] += 1;
                $leveldocuments[$levelKey]['Unreleasedcount'] += 1;
                $kinddocuments[$kindKey]['Unreleasedcount'] += 1;
            }





        }






        // untuk update realisasi
        // untuk update realisasi
        foreach ($newProgressReports as $newProgressReport) {
            $unitKey = 'unit' . $newProgressReport['newreport_id'];
            $levelKey = $newProgressReport['level_id'] . '-' . $newProgressReport['newreport_id'];
            $kindKey = $newProgressReport['documentkind_id'] . '-' . $newProgressReport['newreport_id'] . '-' . $newProgressReport['level_id'];

            // tingkat unit
            $date = optional($newProgressReport->histories->first())->created_at ?? $newProgressReport->updated_at;

            $deadlineDate = Carbon::parse($date)->format('Y-m-d');

            $existingDeadline = $units[$unitKey]['end_real'] ?? null;
            if (!$existingDeadline || $deadlineDate < $existingDeadline) {
                $units[$unitKey]['end_real'] = $deadlineDate;
            }
            $units[$unitKey]['start_real'] = $units[$unitKey]['start'];

            $existingDeadline = $leveldocuments[$levelKey]['end_real'] ?? null;
            if (!$existingDeadline || $deadlineDate < $existingDeadline) {
                $leveldocuments[$levelKey]['end_real'] = $deadlineDate;
            }
            $leveldocuments[$levelKey]['start_real'] = $leveldocuments[$levelKey]['start'];

            $existingDeadline = $units[$unitKey]['end_real'] ?? null;
            if (!$existingDeadline || $deadlineDate < $existingDeadline) {
                $units[$unitKey]['end_real'] = $deadlineDate;
            }
            $units[$unitKey]['start_real'] = $units[$unitKey]['start'];
        }


        foreach ($newProgressReportsall as $newProgressReport) {
            $unitKey = 'unit' . $newProgressReport['newreport_id'];

            // Skip jika $units[$unitKey] tidak ada
            if (!isset($units[$unitKey])) {
                continue;
            }

            // Inisialisasi nilai jika belum ada untuk real_Releasedcount dan real_Unreleasedcount
            if (!isset($units[$unitKey]['real_Releasedcount'])) {
                $units[$unitKey]['real_Releasedcount'] = 0;
            }
            if (!isset($units[$unitKey]['real_Unreleasedcount'])) {
                $units[$unitKey]['real_Unreleasedcount'] = 0;
            }
            if (!isset($units[$unitKey]['workload'])) {
                $units[$unitKey]['workload'] = 0;
            }


            // Inisialisasi nilai jika belum ada untuk real_Releasedcount dan real_Unreleasedcount pada $technology
            if (!isset($technology['real_Releasedcount'])) {
                $technology['real_Releasedcount'] = 0;
            }
            if (!isset($technology['real_Unreleasedcount'])) {
                $technology['real_Unreleasedcount'] = 0;
            }


            // Proses berdasarkan status
            if ($newProgressReport['status'] == "RELEASED") {
                $technology['real_Releasedcount'] += 1;
                $units[$unitKey]['real_Releasedcount'] += 1;
            } else {
                $technology['real_Unreleasedcount'] += 1;
                $units[$unitKey]['real_Unreleasedcount'] += 1;
            }

            if (isset($newProgressReport['papersize'])) {
                $units[$unitKey]['workload'] += self::workloadcount($newProgressReport['papersize'], $newProgressReport['sheet'], $newProgressReport['rev'], 0);
            }
        }












        $technology['start'] = self::parseDate($technology['start']);
        $technology['end'] = self::parseDate($technology['end']);
        $technology['completed'] = ['amount' => 1,];
        $technology['completed_real'] = self::calculateCompletionPercentage($technology['Releasedcount'], $technology['Unreleasedcount']);

        $technology['color'] = self::setColor($technology['Releasedcount'], $technology['Unreleasedcount'], 'plan');
        $technology['start_real'] = $technology['start'];
        $technology['end_real'] = [now()->year, now()->month - 1, now()->day];
        $technology['color_real'] = self::setColor($technology['Releasedcount'], $technology['Unreleasedcount'], 'real');


        // $technology['zones'] = [
        //     [
        //         'value' => Carbon::create($technology['start_real'][0], $technology['start_real'][1], $technology['start_real'][2])
        //             ->addSeconds(
        //                 Carbon::create($technology['end_real'][0], $technology['end_real'][1], $technology['end_real'][2])
        //                     ->diffInSeconds(Carbon::create($technology['start_real'][0], $technology['start_real'][1], $technology['start_real'][2]))
        //                 / 2
        //             )
        //             ->toIso8601String(), // Titik tengah
        //         'color' => 'red' // Warna pertama (merah)
        //     ],
        //     [
        //         'color' => 'green' // Warna kedua (hijau)
        //     ]
        // ];



        if ($project != 'KCI') {
            if (($technology['real_Releasedcount'] + $technology['real_Unreleasedcount']) == $technology['Releasedcount'] + $technology['Unreleasedcount']) {
                $technology['sinkronstatus'] = "- Sinkron";
            } else {
                $technology['sinkronstatus'] = "- Asinkron";
            }
        }



        $projectData[] = $technology;






        foreach ($kinddocuments as $unitKey => $kinddocument) {
            $kinddocuments[$unitKey]['completed'] = self::calculateCompletionPercentage($kinddocuments[$unitKey]['Releasedcount'], $kinddocuments[$unitKey]['Unreleasedcount']);
            $kinddocuments[$unitKey]['completed_real'] = self::calculateCompletionPercentage($technology['Releasedcount'], $technology['Unreleasedcount']);

            $kinddocuments[$unitKey]['color'] = self::setColor($kinddocuments[$unitKey]['Releasedcount'], $kinddocuments[$unitKey]['Unreleasedcount'], 'plan');


            if ($project != 'KCIA') {
                $projectData[] = $kinddocuments[$unitKey];
                // Level 5 - Detail Dokumen
            }
        }


        foreach ($units as $unitKey => $unit) {
            $units[$unitKey]['start'] = self::parseDate($unit['start']);
            $units[$unitKey]['end'] = self::parseDate($unit['end']);

            $units[$unitKey]['start_real'] = self::parseDate($unit['start_real']);
            $units[$unitKey]['end_real'] = self::parseDate($unit['end_real']);


            $units[$unitKey]['completed'] = ['amount' => 1,];
            $units[$unitKey]['completed_real'] = self::calculateCompletionPercentage($units[$unitKey]['real_Releasedcount'], $units[$unitKey]['real_Unreleasedcount']);
            $units[$unitKey]['color'] = self::setColor($units[$unitKey]['Releasedcount'], $units[$unitKey]['Unreleasedcount'], 'plan');
            $units[$unitKey]['color_real'] = self::setColor($units[$unitKey]['Releasedcount'], $units[$unitKey]['Unreleasedcount'], 'real');

            if ($units[$unitKey]['completed_real']['amount'] != 1) {
                $units[$unitKey]['end_real'] = [now()->year, now()->month - 1, now()->day];
            }
            if ($project != 'KCI') {
                if (($units[$unitKey]['real_Releasedcount'] + $units[$unitKey]['real_Unreleasedcount']) == $units[$unitKey]['Releasedcount'] + $units[$unitKey]['Unreleasedcount']) {
                    $units[$unitKey]['sinkronstatus'] = "- Sinkron";
                } else {
                    $units[$unitKey]['sinkronstatus'] = "- Asinkron";
                }
            }

            $projectData[] = $units[$unitKey];

        }

        foreach ($leveldocuments as $unitKey => $level) {
            $leveldocuments[$unitKey]['start'] = self::parseDate($level['start']);
            $leveldocuments[$unitKey]['end'] = self::parseDate($level['end']);
            $leveldocuments[$unitKey]['completed'] = ['amount' => 1,];
            $leveldocuments[$unitKey]['completed_real'] = self::calculateCompletionPercentage($leveldocuments[$unitKey]['Releasedcount'], $leveldocuments[$unitKey]['Unreleasedcount']);

            $leveldocuments[$unitKey]['color'] = self::setColor($leveldocuments[$unitKey]['Releasedcount'], $leveldocuments[$unitKey]['Unreleasedcount'], 'plan');
            $leveldocuments[$unitKey]['collapsed'] = True;
            $leveldocuments[$unitKey]['start_real'] = self::parseDate($level['start_real']);
            $leveldocuments[$unitKey]['end_real'] = self::parseDate($level['end_real']);
            $leveldocuments[$unitKey]['color_real'] = self::setColor($leveldocuments[$unitKey]['Releasedcount'], $leveldocuments[$unitKey]['Unreleasedcount'], 'real');
            if ($leveldocuments[$unitKey]['completed_real']['amount'] != 1) {
                $leveldocuments[$unitKey]['end_real'] = [now()->year, now()->month - 1, now()->day];
            }


            if ($project != 'KCIA') {
                $projectData[] = $leveldocuments[$unitKey];
                // Level 5 - Detail Dokumen
            }
        }
        return $projectData;






    }




    public static function getProjectData($project)
    {

        $availableDocumentNames = NewProgressReportDocumentKind::pluck('name', 'id');
        $levels = NewProgressReportsLevel::pluck('title', 'id');
        $projectType = ProjectType::where('title', $project)->first();


        $projectData = []; // Data proyek untuk response JSON
        $leveldocuments = [];
        $kinddocuments = [];

        // Level 1 - Divisi
        $technology = [
            'id' => '1',
            'name' => 'TECHNOLOGY',
            // 'collapsed' => true,
            'Releasedcount' => 0,
            'Unreleasedcount' => 0,
            'fontSymbol' => 'smile-o',
        ];

        $units = [];


        $newReports = $projectType->newreports; // Menggunakan relasi

        $newReportIds = $newReports->pluck('id')->toArray();

        $newProgressReportsall = NewProgressReport::with(['newreport', 'histories'])
            ->whereIn('newreport_id', $newReportIds)
            ->get();  // Ambil data terlebih dahulu

        // Proses koleksi untuk memfilter berdasarkan kondisi whereNotNull
        $newProgressReports = $newProgressReportsall->filter(function ($item) {
            return !is_null($item->documentkind_id) &&
                !is_null($item->level_id) &&
                !is_null($item->startreleasedate) &&
                !is_null($item->deadlinereleasedate);
        });


        $documentKeys = [];


        foreach ($newProgressReports as $newProgressReport) {
            $unitKey = 'unit' . $newProgressReport['newreport_id'];
            $units[$unitKey] = [
                'id' => 'unit' . $newProgressReport['newreport_id'],
                'name' => $newProgressReport->newreport->unit,
                'collapsed' => true,
                'parent' => '1',
                'Releasedcount' => 0,
                'Unreleasedcount' => 0,
            ];
        }

        foreach ($newProgressReports as $newProgressReport) {
            // Gabungkan ketiga variabel menjadi satu kunci unik
            $key = $newProgressReport['documentkind_id'] . '-' . $newProgressReport['newreport_id'] . '-' . $newProgressReport['level_id'];


            // Simpan data berdasarkan kunci unik
            $startDate = $newProgressReport['startreleasedate'];
            $deadlineDate = $newProgressReport['deadlinereleasedate'];

            // Parse tanggal agar dapat dibandingkan dengan benar
            $startDateTimestamp = strtotime($startDate);
            $deadlineDateTimestamp = strtotime($deadlineDate);

            // Jika kunci belum ada, inisialisasi
            if (!isset($documentKeys[$key])) {
                $documentKeys[$key] = [
                    'unit' => $newProgressReport->newreport->unit,
                    'documentkind_id' => $newProgressReport['documentkind_id'],
                    'newreport_id' => $newProgressReport['newreport_id'],
                    'level_id' => $newProgressReport['level_id'],
                    'start' => date('Y-m-d H:i:s', $startDateTimestamp),
                    'end' => date('Y-m-d H:i:s', $deadlineDateTimestamp),
                ];
            } else {
                // Selalu update tanggal mulai jika tanggal baru lebih awal
                if ($startDateTimestamp <= strtotime($documentKeys[$key]['start'])) {
                    $documentKeys[$key]['start'] = date('Y-m-d H:i:s', $startDateTimestamp);
                }

                // Selalu update tanggal selesai jika tanggal baru lebih akhir
                if ($deadlineDateTimestamp >= strtotime($documentKeys[$key]['end'])) {
                    $documentKeys[$key]['end'] = date('Y-m-d H:i:s', $deadlineDateTimestamp);
                }
            }


            // Update technology start and end
            if (!isset($technology['start']) || empty($technology['start'])) {
                $technology['start'] = date('Y-m-d H:i:s', $startDateTimestamp);
            } else {
                if ($startDateTimestamp < strtotime($technology['start'])) {
                    $technology['start'] = date('Y-m-d H:i:s', $startDateTimestamp);
                }
            }

            if (!isset($technology['end']) || empty($technology['end'])) {
                $technology['end'] = date('Y-m-d H:i:s', $deadlineDateTimestamp);
            } else {
                if ($deadlineDateTimestamp > strtotime($technology['end'])) {
                    $technology['end'] = date('Y-m-d H:i:s', $deadlineDateTimestamp);
                }
            }


            $unitKey = 'unit' . $newProgressReport['newreport_id'];


            // Update $units['unit' . $newProgressReport['newreport_id']] start and end
            // Update tanggal mulai dan selesai untuk unit terkait
            if (!empty($newProgressReport['startreleasedate'])) {
                $startDate = strtotime($newProgressReport['startreleasedate']);
                if (empty($units[$unitKey]['start']) || $startDate < strtotime($units[$unitKey]['start'])) {
                    $units[$unitKey]['start'] = date('Y-m-d H:i:s', $startDate);
                }
            }

            if (!empty($newProgressReport['deadlinereleasedate'])) {
                $deadlineDate = strtotime($newProgressReport['deadlinereleasedate']);
                if (empty($units[$unitKey]['end']) || $deadlineDate > strtotime($units[$unitKey]['end'])) {
                    $units[$unitKey]['end'] = date('Y-m-d H:i:s', $deadlineDate);
                }
            }


        }


        $uniqueDocuments = $documentKeys;


        foreach ($uniqueDocuments as $uniqueKey => $document) {
            $levelKey = $document['level_id'] . '-' . $document['newreport_id'];
            if (!isset($leveldocuments[$levelKey])) {
                $leveldocuments[$levelKey] = [
                    'id' => $levelKey,
                    'name' => $levels[$document['level_id']],
                    'collapsed' => true,
                    'parent' => 'unit' . $document['newreport_id'],
                    'Releasedcount' => 0,
                    'Unreleasedcount' => 0,
                ];

                $startDate = strtotime($document['start']);
                if (empty($leveldocuments[$levelKey]['start']) || $startDate < strtotime($leveldocuments[$levelKey]['start'])) {
                    $leveldocuments[$levelKey]['start'] = date('Y-m-d H:i:s', $startDate);
                }

                $deadlineDate = strtotime($document['end']);
                if (empty($leveldocuments[$levelKey]['end']) || $deadlineDate > strtotime($leveldocuments[$levelKey]['end'])) {
                    $leveldocuments[$levelKey]['end'] = date('Y-m-d H:i:s', $deadlineDate);
                }
            }



            $kindKey = $document['documentkind_id'] . '-' . $document['newreport_id'] . '-' . $document['level_id'];

            if (!isset($kinddocuments[$kindKey])) {
                $kinddocuments[$kindKey] = [
                    'id' => $kindKey,
                    'name' => $availableDocumentNames[$document['documentkind_id']] . " (" . $levels[$document['level_id']] . ")",
                    'start' => self::parseDate($document['start']),
                    'end' => self::parseDate($document['end']),
                    'collapsed' => true,
                    'parent' => $levelKey,
                    'Releasedcount' => 0,
                    'Unreleasedcount' => 0,
                ];
            }
        }




        foreach ($newProgressReports as $newProgressReport) {

            $data = [
                'id' => $newProgressReport['id'],
                'name' => $newProgressReport['nodokumen'] . "-" . $newProgressReport['namadokumen'],
                'collapsed' => true,
                'parent' => $newProgressReport['documentkind_id'] . '-' . $newProgressReport['newreport_id'] . '-' . $newProgressReport['level_id'],
                'completed' => self::calculateCompletionPercentage($newProgressReport['status'] === 'RELEASED' ? 1 : 0, 0),
                'color' => self::setColor($newProgressReport['status'] === 'RELEASED' ? 1 : 0, 0, 'plan'),

            ];
            if (!empty($newProgressReport['startreleasedate'])) {
                $startDate = self::parseDate($newProgressReport['startreleasedate']);
                $data['start'] = $startDate;
            }

            if (!empty($newProgressReport['deadlinereleasedate'])) {
                $deadlineDate = self::parseDate($newProgressReport['deadlinereleasedate']);
                $data['end'] = $deadlineDate;
            }


            if ($project != 'KCIA') {
                $projectData[] = $data;
                // Level 5 - Detail Dokumen
            }



            $unitKey = 'unit' . $newProgressReport['newreport_id'];
            $levelKey = $newProgressReport['level_id'] . '-' . $newProgressReport['newreport_id'];
            $kindKey = $newProgressReport['documentkind_id'] . '-' . $newProgressReport['newreport_id'] . '-' . $newProgressReport['level_id'];

            if ($newProgressReport['status'] == "RELEASED") {
                $technology['Releasedcount'] += 1;
                $units[$unitKey]['Releasedcount'] += 1;
                $leveldocuments[$levelKey]['Releasedcount'] += 1;
                $kinddocuments[$kindKey]['Releasedcount'] += 1;






            } else {
                $technology['Unreleasedcount'] += 1;
                $units[$unitKey]['Unreleasedcount'] += 1;
                $leveldocuments[$levelKey]['Unreleasedcount'] += 1;
                $kinddocuments[$kindKey]['Unreleasedcount'] += 1;
            }





        }






        // untuk update realisasi
        // untuk update realisasi
        foreach ($newProgressReports as $newProgressReport) {
            $unitKey = 'unit' . $newProgressReport['newreport_id'];
            $levelKey = $newProgressReport['level_id'] . '-' . $newProgressReport['newreport_id'];
            $kindKey = $newProgressReport['documentkind_id'] . '-' . $newProgressReport['newreport_id'] . '-' . $newProgressReport['level_id'];

            // tingkat unit
            $date = optional($newProgressReport->histories->first())->created_at ?? $newProgressReport->updated_at;

            $deadlineDate = Carbon::parse($date)->format('Y-m-d');

            $existingDeadline = $units[$unitKey]['end_real'] ?? null;
            if (!$existingDeadline || $deadlineDate < $existingDeadline) {
                $units[$unitKey]['end_real'] = $deadlineDate;
            }
            $units[$unitKey]['start_real'] = $units[$unitKey]['start'];

            $existingDeadline = $leveldocuments[$levelKey]['end_real'] ?? null;
            if (!$existingDeadline || $deadlineDate < $existingDeadline) {
                $leveldocuments[$levelKey]['end_real'] = $deadlineDate;
            }
            $leveldocuments[$levelKey]['start_real'] = $leveldocuments[$levelKey]['start'];

            $existingDeadline = $units[$unitKey]['end_real'] ?? null;
            if (!$existingDeadline || $deadlineDate < $existingDeadline) {
                $units[$unitKey]['end_real'] = $deadlineDate;
            }
            $units[$unitKey]['start_real'] = $units[$unitKey]['start'];
        }


        foreach ($newProgressReportsall as $newProgressReport) {
            $unitKey = 'unit' . $newProgressReport['newreport_id'];

            // Skip jika $units[$unitKey] tidak ada
            if (!isset($units[$unitKey])) {
                continue;
            }

            // Inisialisasi nilai jika belum ada untuk real_Releasedcount dan real_Unreleasedcount
            if (!isset($units[$unitKey]['real_Releasedcount'])) {
                $units[$unitKey]['real_Releasedcount'] = 0;
            }
            if (!isset($units[$unitKey]['real_Unreleasedcount'])) {
                $units[$unitKey]['real_Unreleasedcount'] = 0;
            }
            if (!isset($units[$unitKey]['workload'])) {
                $units[$unitKey]['workload'] = 0;
            }


            // Inisialisasi nilai jika belum ada untuk real_Releasedcount dan real_Unreleasedcount pada $technology
            if (!isset($technology['real_Releasedcount'])) {
                $technology['real_Releasedcount'] = 0;
            }
            if (!isset($technology['real_Unreleasedcount'])) {
                $technology['real_Unreleasedcount'] = 0;
            }


            // Proses berdasarkan status
            if ($newProgressReport['status'] == "RELEASED") {
                $technology['real_Releasedcount'] += 1;
                $units[$unitKey]['real_Releasedcount'] += 1;
            } else {
                $technology['real_Unreleasedcount'] += 1;
                $units[$unitKey]['real_Unreleasedcount'] += 1;
            }

            if (isset($newProgressReport['papersize'])) {
                $units[$unitKey]['workload'] += self::workloadcount($newProgressReport['papersize'], $newProgressReport['sheet'], $newProgressReport['rev'], 0);
            }
        }












        $technology['start'] = self::parseDate($technology['start']);
        $technology['end'] = self::parseDate($technology['end']);
        $technology['completed'] = ['amount' => 1,];
        $technology['completed_real'] = self::calculateCompletionPercentage($technology['Releasedcount'], $technology['Unreleasedcount']);

        $technology['color'] = self::setColor($technology['Releasedcount'], $technology['Unreleasedcount'], 'plan');
        $technology['start_real'] = $technology['start'];
        $technology['end_real'] = [now()->year, now()->month - 1, now()->day];
        $technology['color_real'] = self::setColor($technology['Releasedcount'], $technology['Unreleasedcount'], 'real');


        // $technology['zones'] = [
        //     [
        //         'value' => Carbon::create($technology['start_real'][0], $technology['start_real'][1], $technology['start_real'][2])
        //             ->addSeconds(
        //                 Carbon::create($technology['end_real'][0], $technology['end_real'][1], $technology['end_real'][2])
        //                     ->diffInSeconds(Carbon::create($technology['start_real'][0], $technology['start_real'][1], $technology['start_real'][2]))
        //                 / 2
        //             )
        //             ->toIso8601String(), // Titik tengah
        //         'color' => 'red' // Warna pertama (merah)
        //     ],
        //     [
        //         'color' => 'green' // Warna kedua (hijau)
        //     ]
        // ];



        if ($project != 'KCI') {
            if (($technology['real_Releasedcount'] + $technology['real_Unreleasedcount']) == $technology['Releasedcount'] + $technology['Unreleasedcount']) {
                $technology['sinkronstatus'] = "- Sinkron";
            } else {
                $technology['sinkronstatus'] = "- Asinkron";
            }
        }



        $projectData[] = $technology;






        foreach ($kinddocuments as $unitKey => $kinddocument) {
            $kinddocuments[$unitKey]['completed'] = self::calculateCompletionPercentage($kinddocuments[$unitKey]['Releasedcount'], $kinddocuments[$unitKey]['Unreleasedcount']);
            $kinddocuments[$unitKey]['completed_real'] = self::calculateCompletionPercentage($technology['Releasedcount'], $technology['Unreleasedcount']);

            $kinddocuments[$unitKey]['color'] = self::setColor($kinddocuments[$unitKey]['Releasedcount'], $kinddocuments[$unitKey]['Unreleasedcount'], 'plan');


            if ($project != 'KCIA') {
                $projectData[] = $kinddocuments[$unitKey];
                // Level 5 - Detail Dokumen
            }
        }


        foreach ($units as $unitKey => $unit) {
            $units[$unitKey]['start'] = self::parseDate($unit['start']);
            $units[$unitKey]['end'] = self::parseDate($unit['end']);

            $units[$unitKey]['start_real'] = self::parseDate($unit['start_real']);
            $units[$unitKey]['end_real'] = self::parseDate($unit['end_real']);


            $units[$unitKey]['completed'] = ['amount' => 1,];
            $units[$unitKey]['completed_real'] = self::calculateCompletionPercentage($units[$unitKey]['real_Releasedcount'], $units[$unitKey]['real_Unreleasedcount']);
            $units[$unitKey]['color'] = self::setColor($units[$unitKey]['Releasedcount'], $units[$unitKey]['Unreleasedcount'], 'plan');
            $units[$unitKey]['color_real'] = self::setColor($units[$unitKey]['Releasedcount'], $units[$unitKey]['Unreleasedcount'], 'real');

            if ($units[$unitKey]['completed_real']['amount'] != 1) {
                $units[$unitKey]['end_real'] = [now()->year, now()->month - 1, now()->day];
            }
            if ($project != 'KCI') {
                if (($units[$unitKey]['real_Releasedcount'] + $units[$unitKey]['real_Unreleasedcount']) == $units[$unitKey]['Releasedcount'] + $units[$unitKey]['Unreleasedcount']) {
                    $units[$unitKey]['sinkronstatus'] = "- Sinkron";
                } else {
                    $units[$unitKey]['sinkronstatus'] = "- Asinkron";
                }
            }

            $projectData[] = $units[$unitKey];

        }

        foreach ($leveldocuments as $unitKey => $level) {
            $leveldocuments[$unitKey]['start'] = self::parseDate($level['start']);
            $leveldocuments[$unitKey]['end'] = self::parseDate($level['end']);
            $leveldocuments[$unitKey]['completed'] = ['amount' => 1,];
            $leveldocuments[$unitKey]['completed_real'] = self::calculateCompletionPercentage($leveldocuments[$unitKey]['Releasedcount'], $leveldocuments[$unitKey]['Unreleasedcount']);

            $leveldocuments[$unitKey]['color'] = self::setColor($leveldocuments[$unitKey]['Releasedcount'], $leveldocuments[$unitKey]['Unreleasedcount'], 'plan');
            $leveldocuments[$unitKey]['collapsed'] = True;
            $leveldocuments[$unitKey]['start_real'] = self::parseDate($level['start_real']);
            $leveldocuments[$unitKey]['end_real'] = self::parseDate($level['end_real']);
            $leveldocuments[$unitKey]['color_real'] = self::setColor($leveldocuments[$unitKey]['Releasedcount'], $leveldocuments[$unitKey]['Unreleasedcount'], 'real');
            if ($leveldocuments[$unitKey]['completed_real']['amount'] != 1) {
                $leveldocuments[$unitKey]['end_real'] = [now()->year, now()->month - 1, now()->day];
            }


            if ($project != 'KCIA') {
                $projectData[] = $leveldocuments[$unitKey];
                // Level 5 - Detail Dokumen
            }
        }
        return $projectData;






    }








    public static function getHoursProjectData($year)
    {
        // Ambil data NewProgressReportHistory dengan filter tahun dan validasi
        $newProgressReportHistories = Newprogressreporthistory::with('newProgressReport.newreport.projectType')->whereYear('realisasidate', $year)->get()
            ->filter(function ($item) {
                return !is_null($item->realisasidate) &&
                    !is_null($item->rev);
            });



        $monthlyWorkload = [];

        // Loop utama untuk menghitung workload
        foreach ($newProgressReportHistories as $history) {
            $date = Carbon::parse($history->realisasidate)->format('Y-m');
            $projectName = $history->newProgressReport->newreport->projectType->title ?? 'Unknown';

            $releasedagain = $history->newProgressReport->releasedagain ?? 0;

            if (!isset($monthlyWorkload[$projectName][$date])) {
                $monthlyWorkload[$projectName][$date] = 0;
            }

            if (isset($history->papersize)) {
                $monthlyWorkload[$projectName][$date] += self::workloadcount($history->papersize, $history->sheet, $history->rev, $releasedagain);
            }
        }

        $projectData = [];
        foreach ($monthlyWorkload as $projectName => $dates) {
            foreach ($dates as $date => $workload) {
                $projectData[] = [
                    'project' => $projectName,
                    'date' => $date,
                    'workload' => $workload,
                ];
            }
        }

        return $projectData;
    }


    public static function getHoursProjectDatabyProject($projectTitle)
    {
        // Ambil data NewProgressReportHistory dengan relasi dan filter hanya yang valid
        // syarat rev, papersize, dan sheet
        $newProgressReportHistories = Newprogressreporthistory::with('newProgressReport.newreport.projectType')
            ->get()
            ->filter(function ($item) use ($projectTitle) {
                return !is_null($item->rev) && !is_null($item->papersize) && !is_null($item->sheet) &&
                    isset($item->newProgressReport->newreport->projectType->title) &&
                    $item->newProgressReport->newreport->projectType->title === $projectTitle;
            });

        $totalWorkload = 0;

        // Loop utama untuk menghitung workload
        foreach ($newProgressReportHistories as $history) {
            $releasedagain = $history->newProgressReport->releasedagain ?? 0;

            // Hitung workload jika properti papersize ada
            if (isset($history->papersize)) {
                $totalWorkload += self::workloadcount(
                    $history->papersize,
                    $history->sheet,
                    $history->rev,
                    $releasedagain
                );
            }
        }

        $totalworkload = number_format($totalWorkload, 2);



        $monthlyWorkload = [];

        // Loop utama untuk menghitung workload
        foreach ($newProgressReportHistories as $history) {
            $date = Carbon::parse($history->realisasidate)->format('Y-m');
            $projectName = $history->newProgressReport->newreport->projectType->title ?? 'Unknown';

            $releasedagain = $history->newProgressReport->releasedagain ?? 0;

            if (!isset($monthlyWorkload[$projectName][$date])) {
                $monthlyWorkload[$projectName][$date] = 0;
            }

            if (isset($history->papersize)) {
                $monthlyWorkload[$projectName][$date] += self::workloadcount($history->papersize, $history->sheet, $history->rev, $releasedagain);
            }
        }

        $projectData = [];
        foreach ($monthlyWorkload as $projectName => $dates) {
            foreach ($dates as $date => $workload) {
                $projectData[] = [
                    'project' => $projectName,
                    'date' => $date,
                    'workload' => $workload,
                ];
            }
        }

        $hasil = ['totalworkload' => $totalworkload, "montly-year" => $projectData];
        return $hasil;

    }








    public static function setColor($releasedCount, $unreleasedCount, $kind)
    {
        $total = $releasedCount + $unreleasedCount;

        if ($total == 0) {
            return '#ff0000';
        }

        $percentage = $releasedCount / $total;

        if ($kind == 'real') { // Hijau dan Merah (plan)
            return '#ff0000';
        } else {
            return [
                'pattern' => [
                    'path' => [
                        'd' => 'M 10 0 L 0 10',
                        'strokeWidth' => 2,
                        'stroke' => '#000'
                    ],
                    'width' => 10,
                    'height' => 10
                ]
            ];


            // return [
            //     'linearGradient' => ['x1' => 0, 'y1' => 0, 'x2' => 1, 'y2' => 0],
            //     'stops' => [
            //         [0, '#00f'],
            //         [1, '#00f']
            //     ]
            // ];
        }
    }
    public static function parseDate($date)
    {
        if (!$date) {
            return [now()->year, now()->month - 1, now()->day];
        }

        $parts = explode('-', $date);
        if (count($parts) !== 3) {
            return [now()->year, now()->month - 1, now()->day];
        }

        $year = (int) $parts[0];
        $month = (int) ltrim($parts[1], '0') - 1; // Bulan dimulai dari 0
        $day = (int) $parts[2];

        return $year > 2000 ? [$year, $month, $day] : [now()->year, now()->month - 1, now()->day];
    }

    public static function calculateCompletionPercentage($releasedCount, $unreleasedCount)
    {
        // Calculate the total count and avoid division by zero
        $total = $releasedCount + $unreleasedCount;

        // Handle the case where total is zero (gray color indicating no progress)
        if ($total == 0) {
            return [
                'amount' => 0,
                'fill' => '#0b0',
            ];
        }

        // Calculate the completion percentage
        $percentage = $releasedCount / $total;

        // Round the percentage to 2 decimal places
        $percentage = round($percentage, 2);

        // Handle milestone (all tasks released, full completion)
        if ($releasedCount > 0 && $unreleasedCount == 0) {
            return [
                'amount' => 1,

                'fill' => '#0b0',
            ];
        }

        // Return the rounded percentage
        return [
            'amount' => $percentage,

            'fill' => '#0b0',
        ];
    }

    public static function workloadcount($papersize, $sheet, $rev, $releasedagain)
    {

        $revequal = 1;
        if ($rev == "0" && $releasedagain == 0) {
            $revequal = 1;
        } elseif ($rev == "0" && $releasedagain == 1) {
            $revequal = 0.05;
        } else {
            $revequal = 0.5;
        }

        if ($papersize == 'A4') {
            return 4 * $sheet * $revequal;
        }
        if ($papersize == 'A3') {
            return 8 * $sheet * $revequal;
        }
        if ($papersize == 'A2') {
            return 16 * $sheet * $revequal;
        }
        if ($papersize == 'A1') {
            return 32 * $sheet * $revequal;
        }

    }






}
