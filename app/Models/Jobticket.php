<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
class Jobticket extends Model
{
    use HasFactory;

    protected $table = 'jobticket';

    protected $fillable = [
        'jobticket_identity_id',
        'rev',
        'documentname',
        'documentsupport',
        'level',
        'drafter_id',
        'checker_id',
        'approver_id',
        'deadlinerelease',
        'note',
        'inputer_id',
        'publicstatus'
    ];

    // Method untuk mengambil dokumen yang terkait dari Newprogressreporthistory
    public static function getDocumentSupport($documentsupport)
    {
        // Ambil documentsupport dan decode dari format JSON
        $documentIds = json_decode($documentsupport, true);

        if (is_array($documentIds)) {
            // Ambil data dari Newprogressreporthistory berdasarkan ID yang ada di documentsupport
            return Newprogressreporthistory::whereIn('id', $documentIds)->get();
        }

        return collect(); // Mengembalikan collection kosong jika tidak ada dokumen terkait
    }

    public static function getBatchDocumentSupport(Collection $jobtickets)
    {
        // Kumpulkan semua documentsupport dari setiap jobticket
        $documentIds = [];
        foreach ($jobtickets as $jobticket) {
            $ids = json_decode($jobticket->documentsupport, true);
            if (is_array($ids)) {
                $documentIds = array_merge($documentIds, $ids);
            }
        }

        // Jika ada document IDs yang perlu diambil
        if (!empty($documentIds)) {
            // Ambil semua dokumen terkait dalam satu query dan ambil hanya kolom yang diperlukan
            return Newprogressreporthistory::whereIn('id', array_unique($documentIds))
                ->get(['id', 'namadokumen', 'nodokumen', 'rev']); // Mengambil hanya 4 kolom
        }

        return collect(); // Mengembalikan koleksi kosong jika tidak ada ID dokumen
    }

    public function documentKind()
    {
        return $this->belongsTo(JobticketDocumentKind::class, 'jobticket_documentkind_id');
    }

    public function jobticketIdentity()
    {
        return $this->belongsTo(JobticketIdentity::class, 'jobticket_identity_id');
    }

    public function jobticketStarted()
    {
        return $this->hasOne(JobticketStarted::class, 'jobticket_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id', 'drafter_id')
            ->orWhere('id', 'checker_id')
            ->orWhere('id', 'approver_id');
    }

    public function starttugasbaru()
    {
        // Check if jobticketStarted exists; if not, create a new instance
        $datawaktu = $this->jobticketStarted ?: new JobticketStarted();

        // Set the jobticket_id if creating a new record
        if (!$datawaktu->exists) {
            $datawaktu->jobticket_id = $this->id;
        }

        // Update the relevant fields
        $datawaktu->start_time_first = Carbon::now();
        $datawaktu->start_time_run = Carbon::now();
        $datawaktu->pause_time_run = null;
        $datawaktu->total_elapsed_seconds = 0;
        $datawaktu->statusrevisi = 'dibuka';

        // Save the JobticketStarted record
        $datawaktu->save();
    }

    public function pausetugasbaru()
    {
        // Check if jobticketStarted exists; if not, return false or handle as needed
        $datawaktu = $this->jobticketStarted;

        if (!$datawaktu) {
            return false; // or handle this scenario differently
        }

        // Check if start_time_run is set before pausing the task
        if (isset($datawaktu->start_time_run)) {
            $now = Carbon::now();
            $elapsed = $now->diffInSeconds($datawaktu->start_time_run);
            $datawaktu->pause_time_run = $now;
            $datawaktu->total_elapsed_seconds += $elapsed;
        }

        // Save the JobticketStarted record
        $datawaktu->save();

        return true;
    }

    public function selesaitugasbaru()
    {
        // if ($this->drafter_id == null || $this->checker_id == null || $this->approver_id == null) {
        //     return [
        //         'error' => 'Pastikan checker dan approver sudah ditunjuk',
        //     ];
        // }

        $temporystatus = $this->jobticketStarted;

        if ($temporystatus->pause_time_run == null) {
            $this->pausetugasbaru();
            $temporystatus->refresh();
        }

        if ($temporystatus->pause_time_run != null) {
            $pauseTime = new Carbon($temporystatus->pause_time_run);
            $totalElapsedSeconds = $temporystatus->total_elapsed_seconds ?? 0;
            $totalElapsedSeconds += $pauseTime->diffInSeconds(Carbon::now());

            $revisionCount = $this->jobticketStarted->revisions()->count();
            $revisionName = $revisionCount;

            $newRevisionData = [
                'revisionname' => $revisionName,
                'start_time_run' => $temporystatus->start_time_first,
                'end_time_run' => Carbon::now(),
                'revision_status' => "belum divalidasi",
                'total_elapsed_seconds' => $totalElapsedSeconds,
                'drafter_id' => $this->drafter_id,
                'checker_id' => $this->checker_id ?? null,
                'approver_id' => $this->approver_id ?? null,
            ];

            // Simpan data revisi baru dan dapatkan ID-nya
            $newRevision = $this->jobticketStarted->revisions()->create($newRevisionData);
            $newRevisionId = $newRevision->id;

            $temporystatus->start_time_first = null;
            $temporystatus->start_time_run = null;
            $temporystatus->pause_time_run = null;
            $temporystatus->total_elapsed_seconds = 0;
            $temporystatus->statusrevisi = "ditutup";
            $temporystatus->revisionlast = $revisionName;
            $temporystatus->save();

            return [
                'success' => 'Data berhasil diperbarui',
                'elapsedSeconds' => $totalElapsedSeconds,
                'lastKey' => $revisionName,
                'newRevisionId' => $newRevisionId, // ID revisi baru untuk referensi file
            ];
        } else {
            return [
                'error' => 'Gagal menjeda tugas, coba lagi.',
            ];
        }
    }

    public function resumetugasbaru()
    {
        $datawaktu = $this->jobticketStarted;
        if (isset($datawaktu->pause_time_run)) {
            $datawaktu->start_time_run = Carbon::now();
            $datawaktu->pause_time_run = null;
        }
        $datawaktu->save();
        $pauseTime = new Carbon($datawaktu->pause_time_run);
        $currentElapsedSeconds = $datawaktu->total_elapsed_seconds + $pauseTime->diffInSeconds(Carbon::now());

        return [
            'startTime' => Carbon::now(),
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $currentElapsedSeconds
        ];
    }

    public function izinkanrevisitugasbaru()
    {

        $datawaktu = $this->jobticketStarted;
        $datawaktu->statusrevisi = "dibuka";
        $datawaktu->save();
        $pauseTime = new Carbon($datawaktu->pause_time);
        $currentElapsedSeconds = $datawaktu->total_elapsed_seconds + $pauseTime->diffInSeconds(Carbon::now());
        return [
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $currentElapsedSeconds
        ];
    }

    public static function convertToAlphabetic($number)
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

    public static function UpAlphabetic($rev)
    {
        // Jika input adalah 0 atau '0', kembalikan 'A'
        if ($rev == 0 || $rev == '0') {
            return 'A';
        }

        // Jika input adalah satu huruf, kembalikan huruf setelahnya
        if (is_string($rev) && ctype_alpha($rev)) {
            $rev = strtoupper($rev); // pastikan huruf besar
            // Jika input adalah Z, kembalikan 'AA'
            if ($rev === 'Z') {
                return 'AA';
            }

            return ++$rev; // kembalikan huruf setelahnya
        }

        // Untuk input angka, lakukan konversi ke urutan alfabet
        $alphabet = range('A', 'Z');
        $result = '';

        while ($rev > 0) {
            $remainder = ($rev - 1) % 26;
            $result = $alphabet[$remainder] . $result;
            $rev = (int) (($rev - $remainder) / 26);
        }

        return $result;
    }

    public static function isUpRevision($revWillInput, $revHaveInput)
    {
        if ($revHaveInput === null) {
            return true;
        }

        // Ubah inputan menjadi urutan angka untuk pembandingan
        $convertToOrder = function ($revision) {
            if ($revision == 0 || $revision === '0') {
                return 0; // Jika revisi adalah angka 0
            }

            if (is_string($revision) && ctype_alpha($revision)) {
                $revision = strtoupper($revision); // Pastikan huruf besar
                $order = 0;

                // Konversi huruf ke urutan angka (A=1, B=2, ..., Z=26, AA=27, AB=28, ...)
                for ($i = 0; $i < strlen($revision); $i++) {
                    $order = $order * 26 + (ord($revision[$i]) - ord('A') + 1);
                }

                return $order;
            }

            // Jika format tidak dikenali, lempar error atau kembalikan 0
            return is_numeric($revision) ? intval($revision) : 0;
        };

        $orderWillInput = $convertToOrder($revWillInput);
        $orderHaveInput = $convertToOrder($revHaveInput);

        return $orderWillInput > $orderHaveInput;
    }


    public function reasons()
    {
        return $this->hasMany(Jobticketreason::class, 'jobticket_id');
    }

    public static function drafterCheckerApprover($jobtickets, $userOnly)
    {
        // Filter untuk drafter
        $drafterJobtickets = $jobtickets->filter(function ($jobticket) use ($userOnly) {
            return $jobticket->drafter_id === $userOnly->id;
        });

        // Filter untuk checker
        $checkerJobtickets = $jobtickets->filter(function ($jobticket) use ($userOnly) {
            // Validasi apakah jobticketStarted dan revisions tersedia
            if ($jobticket->jobticketStarted && $jobticket->jobticketStarted->revisions) {
                $revisions = $jobticket->jobticketStarted->revisions;
                // Cek apakah ada checker_status yang null
                $hasNullCheckerStatus = $revisions->contains(fn($revision) => $revision->checker_status === null);

                return $jobticket->checker_id === $userOnly->id && $hasNullCheckerStatus;
            }
            return false;
        });

        // Filter untuk approver
        $approverJobtickets = $jobtickets->filter(function ($jobticket) use ($userOnly) {
            // Validasi apakah jobticketStarted dan revisions tersedia
            if ($jobticket->jobticketStarted && $jobticket->jobticketStarted->revisions) {
                $revisions = $jobticket->jobticketStarted->revisions;
                // Cek apakah checker_status tidak null dan approver_status null
                $hasValidStatuses = $revisions->contains(
                    fn($revision) =>
                    $revision->checker_status !== null && $revision->approver_status === null
                );

                return $jobticket->approver_id === $userOnly->id && $hasValidStatuses;
            }
            return false;
        });

        // Mengembalikan data dalam array
        return [
            'drafterJobtickets' => $drafterJobtickets,
            'checkerJobtickets' => $checkerJobtickets,
            'approverJobtickets' => $approverJobtickets,
        ];
    }

    // Pada model Jobticket



}
