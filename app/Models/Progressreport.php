<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Progressreport extends Model
{
    use HasFactory;

    protected $fillable = [
        'progressreportname',
        'proyek_type',
        'revisi',
        'status',
        'linkscript',
        'linkspreadsheet'
    ];
    // Method to reset a specific document's drafter and timeline
    
    public function resetDocument($namadokumen)
    {
        // Decode JSON fields from the Progressreport model
        $revisiData = json_decode($this->revisi, true)??[];
        $datawaktu = json_decode($this->timeline, true) ?? [];
        
        list($index, $nodokumen) = $this->findDocument($revisiData, $namadokumen);

        // If document not found, return false
        if ($index === -1) {
            return false;
        }

        // Update timeline data
        $datawaktu[$nodokumen] = [];

        // Update the drafter in the revision data
        $revisiData[$index]['drafter'] = null;

        // Encode the updated data back to JSON and save the Progressreport model
        $this->revisi = json_encode($revisiData);
        $this->timeline = json_encode($datawaktu);
        $this->save();

        return true;
    }

    public function picktugasDocument($namadokumen, $name)
    {
        $revisiData = json_decode($this->revisi, true);
       list($index, $nodokumen) = $this->findDocument($revisiData, $namadokumen);

        if ($index === -1) {
            throw new ModelNotFoundException('Document not found');
        }

        $revisiData[$index]['drafter'] = $name;

        $this->revisi = json_encode($revisiData);
        $this->save();
    }

    public function detailtugasDocument($namadokumen, $name)
    {
        try {
            // Decode revision data and timeline data from JSON
            $datawaktu = json_decode($this->timeline, true) ?? [];
            $revisiData = json_decode($this->revisi, true);

            // Find document index
            list($index, $nodokumen) = $this->findDocument($revisiData, $namadokumen);

            // If document not found, return error message
            if ($index === -1) {
                return ['error' => 'Dokumen tidak ditemukan'];
            }


            if (!isset($datawaktu[$nodokumen]['savekonten'])) {
                $datawaktu[$nodokumen]['savekonten'] = [];
            }
            $revisions = $datawaktu[$nodokumen]['savekonten'];

            // Return the data to the controller
            return [
                'success' => true,
                'documentName' => $namadokumen,
                'revisions' => $revisions
            ];
        } catch (\Exception $e) {
            // Handle any unexpected exceptions
            return ['error' => $e->getMessage()];
        }
    }




    public function updateDocument($namadokumen, $name)
    {
        $revisiData = json_decode($this->revisi, true);
        $datawaktu = json_decode($this->timeline, true) ?? [];

        list($index, $nodokumen) = $this->findDocument($revisiData, $namadokumen);

        if ($index === -1) {
            throw new ModelNotFoundException('Document not found');
        }

        $datawaktu[$nodokumen]['start_time_run'] = Carbon::now();
        $datawaktu[$nodokumen]['start_time'] = Carbon::now();
        $datawaktu[$nodokumen]['pause_time'] = null;
        $datawaktu[$nodokumen]['total_elapsed_seconds'] = 0;

        $this->timeline = json_encode($datawaktu);
        $this->save();
    }


    public function pauseDocument($namadokumen)
    {
        // Decode JSON fields from the Progressreport model
        $revisiData = json_decode($this->revisi, true);
        $datawaktu = json_decode($this->timeline, true) ?? [];
        
        list($index, $nodokumen) = $this->findDocument($revisiData, $namadokumen);

        // If document not found, return an error response
        if ($index === -1) {
            return false;
        }

        if (isset($datawaktu[$nodokumen]['start_time'])) {
            $now = Carbon::now();
            $elapsed = $now->diffInSeconds($datawaktu[$nodokumen]['start_time']);

            $datawaktu[$nodokumen]['pause_time'] = $now;
            $datawaktu[$nodokumen]['total_elapsed_seconds'] += $elapsed;
        }

        // Encode the updated data back to JSON and save the Progressreport model
        $this->timeline = json_encode($datawaktu);
        $this->save();

        return true;
    }

    public function resumeTask($namadokumen)
    {
        $revisiData = json_decode($this->revisi, true);
        $datawaktu = json_decode($this->timeline, true) ?? [];

        list($index, $nodokumen) = $this->findDocument($revisiData, $namadokumen);
        
        if ($index === -1) {
            return ['error' => 'Document not found'];
        }

        if (isset($datawaktu[$nodokumen]['pause_time'])) {
            $datawaktu[$nodokumen]['start_time'] = Carbon::now();
            $datawaktu[$nodokumen]['pause_time'] = null;
        }
        $this->timeline = json_encode($datawaktu);
        $this->save();

        $pauseTime = new Carbon($datawaktu[$nodokumen]['pause_time']);
        $currentElapsedSeconds = $datawaktu[$nodokumen]['total_elapsed_seconds'] + $pauseTime->diffInSeconds(Carbon::now());

        return [
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $currentElapsedSeconds
        ];
    }

   

    public function izinkanrevisiTask($namadokumen)
    {
        
        $datawaktu = json_decode($this->timeline, true) ?? [];
        $revisiData = json_decode($this->revisi, true);
        list($index, $nodokumen) = $this->findDocument($revisiData, $namadokumen);
        
        if ($index === -1) {
            return ['error' => 'Document not found'];
        }
        $datawaktu[$nodokumen]['statusrevisi'] = "dibuka";
        $this->timeline = json_encode($datawaktu);
        $this->save();
        $pauseTime = new Carbon($datawaktu[$nodokumen]['pause_time']);
        $currentElapsedSeconds = $datawaktu[$nodokumen]['total_elapsed_seconds'] + $pauseTime->diffInSeconds(Carbon::now());
        return [
            'success' => 'Data berhasil diperbarui',
            'elapsedSeconds' => $currentElapsedSeconds
        ];
    }

    public function findDocument($revisiData, $namadokumen){
        $index = -1;
        $nodokumen = "";

        foreach ($revisiData as $i => $revisi) {
            if ($revisi['namadokumen'] === $namadokumen) {
                $nodokumen = $revisi['nodokumen'];
                $index = $i;
                break;
            }
        }
        return [$index,$nodokumen];
    }
        
    // Metode selesaiTask
    public function selesaiTask($namadokumen)
    {
        try {
            // Decode revision data and timeline data from JSON
            $datawaktu = json_decode($this->timeline, true) ?? [];
            $revisiData = json_decode($this->revisi, true);

            // Find document index
            list($index, $nodokumen) = $this->findDocument($revisiData, $namadokumen);

            // If document not found, return error message
            if ($index === -1) {
                return ['error' => 'Dokumen tidak ditemukan'];
            }

            // Ensure 'savekonten' array exists
            if (!isset($datawaktu[$nodokumen]['savekonten'])) {
                $datawaktu[$nodokumen]['savekonten'] = [];
            }

            // Initialize next revision number
            $nextRevisionNumber = isset($datawaktu[$nodokumen]['savekonten']) ? count($datawaktu[$nodokumen]['savekonten']) : 0;

            // Convert next revision number to alphabetic format (0 -> 0, 1 -> A, 2 -> B, ...)
            $revisionName = $this->convertToAlphabetic($nextRevisionNumber);

            // Create new revision
            $newRevision = [
                'revisionname' => $revisionName,
                'start_time_run' => $datawaktu[$nodokumen]['start_time'] ?? "",
                'end_time_run' => Carbon::now(),
                'revision_status' => "belum divalidasi",
                'total_elapsed_seconds' => $datawaktu[$nodokumen]['total_elapsed_seconds'] ?? 0,
            ];

            // Add new revision to savekonten array
            $datawaktu[$nodokumen]['savekonten'][$revisionName] = $newRevision;

            // Set document properties to mark as completed
            $datawaktu[$nodokumen]['start_time'] = null;
            $datawaktu[$nodokumen]['pause_time'] = null;
            $datawaktu[$nodokumen]['total_elapsed_seconds'] = 0; // Reset to zero
            $datawaktu[$nodokumen]['statusrevisi'] = "ditutup";
            $datawaktu[$nodokumen]['revisionlast'] = $revisionName;

            // Save revision data and timeline data back to JSON
            $this->timeline = json_encode($datawaktu);
            $this->save();

            // Return success message along with total elapsed time and the last key used
            return [
                'success' => 'Data berhasil diperbarui',
                'elapsedSeconds' => $datawaktu[$nodokumen]['total_elapsed_seconds'],
                'lastKey' => $revisionName,
            ];
        } catch (\Exception $e) {
            // Handle any unexpected exceptions
            return ['error' => $e->getMessage()];
        }
    }

    // Metode konversi bilangan ke huruf (1 -> 0, 2 -> A, 27 -> Z, ...)
    private function convertToAlphabetic($number) {
        if($number == 0) {
            return "0";
        }

        $alphabet = "";
        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $alphabet = chr(65 + $remainder) . $alphabet;
            $number = floor(($number - 1) / 26);
        }
        return $alphabet;
    }

    public function revisiData(){
        $revisiData = json_decode($this->revisi, true);
        return $revisiData;
    }
}
