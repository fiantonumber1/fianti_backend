<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newbom extends Model
{
    use HasFactory;

    protected $fillable = ['BOMnumber', 'proyek_type','proyek_type_id','unit'];

    public function newbomkomats()
    {
        return $this->hasMany(Newbomkomat::class);
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }

    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }

    public function bomoneshow($documents, $progressreports, $listdatadocumentencode)
    {
        $groupprogress = [];
        foreach ($progressreports as $progressreport) {
            if (!empty($progressreport->revisi)) {
                $progressitemunit = json_decode($progressreport->revisi, true);
                foreach ($progressitemunit as $item) {
                    $nodokumen = $item['nodokumen'] ?? '';
                    $groupprogress[$nodokumen]['spesifikasi_level'] = !empty($item['level']) ? $item['level'] : "Belum assign level";
                    $groupprogress[$nodokumen]['spesifikasi_pic'] = !empty($item['drafter']) ? $item['drafter'] : "Belum assign pic";
                }
            }
        }
        
        $groupedKomats = [];
        foreach ($documents as $document) {
            $komats = $document->komats;
            $sumberinformasi = json_decode($listdatadocumentencode[$document->id] ?? '[]', true);
            $positionPercentage = $sumberinformasi['positionPercentage'] ?? 0;
            $PEcombineworkstatus = $sumberinformasi['PEcombineworkstatus'] ?? "";

            if (isset($komats)){
                foreach ($komats as $komat) {
                    $komponen = $komat->material;
                    $kodematerial = $komat->kodematerial;
                    $supplier = $komat->supplier;
    
                    if (!isset($groupedKomats[$kodematerial])) {
                        $groupedKomats[$kodematerial] = [
                            'supplier' => [],
                            'komponen' => $komponen,
                            'memoname' => [],
                            'memoid' => [],
                            'memostatus' => [],
                            'percentage' => [],
                            'PEcombineworkstatus' => []
                        ];
                    }
    
                    $groupedKomats[$kodematerial]['supplier'][] = $supplier;
                    $groupedKomats[$kodematerial]['memoname'][] = $document->documentname;
                    $groupedKomats[$kodematerial]['memoid'][] = $document->id;
                    $groupedKomats[$kodematerial]['memostatus'][] = $document->documentstatus;
                    $groupedKomats[$kodematerial]['percentage'][] = $positionPercentage;
                    $groupedKomats[$kodematerial]['PEcombineworkstatus'][] = $PEcombineworkstatus;
                }
            }
            
        }
        
        $revisiall = $this->newbomkomats;
        $seniorpercentage = 0;
        $materialclosed = 0;
        $materialopened = 0;
        $satuanitem = count($revisiall);

        foreach ($revisiall as $item) {
            $kodematerial = $item['kodematerial'] ?? '';
            if (isset($groupedKomats[$kodematerial])) {
                $listpercentage = $groupedKomats[$kodematerial]['percentage'] ?? [];
                $partialpercentage = array_sum($listpercentage) / max(count($listpercentage), 1);
                $seniorpercentage += $partialpercentage / $satuanitem;
                $groupedKomats[$kodematerial]['totalpercentage'] = $partialpercentage;
            }
        }

        foreach ($revisiall as $item) {
            $kodematerial = $item['kodematerial'] ?? '';
            $totalpercentage = $groupedKomats[$kodematerial]['totalpercentage'] ?? 0;
            if ($totalpercentage == 100) {
                $materialclosed++;
            } else {
                $materialopened++;
            }
        }

        return [$groupedKomats, $groupprogress, $seniorpercentage, $materialopened, $materialclosed];
    }



    public static function infoall()
    {
        // Fetch all newboms with their associated newbomkomats
        $newboms = Newbom::with('newbomkomats')->get();

        // Get unique proyek_type_id values and their corresponding titles
        $proyekTypeIds = $newboms->pluck('proyek_type_id')->unique();
        // $projects = ProjectType::whereIn('id', $proyekTypeIds)->get();
        $projects = ProjectType::all();
        $titles = $projects->pluck('title', 'id');

        // Fetch all documents and group them by project_type_id
        $documents = NewMemo::with(['feedbacks', 'komats', 'timelines'])->get();
        $documentsByProyekType = $documents->groupBy('project_type_id');

        // Get additional data for fetched documents
        $additionalData = NewMemo::getAdditionalDataalldocumentdirect($documents);
        $listDataDocumentEncode = $additionalData['listdatadocuments'] ?? [];
        $percentageMemoTerbuka = $additionalData['percentagememoterbuka'] ?? [];
        $percentageMemoTertutup = $additionalData['percentagememotertutup'] ?? [];

        // Initialize arrays
        $groupBomNumberPercentage = [];
        $groupedNewboms = $newboms->groupBy('proyek_type_id');

        // Process each newbom
        foreach ($newboms as $newbom) {
            $projectTitle = $titles->get($newbom->proyek_type_id, 'Unknown Project');
            $documentsForProyekType = $documentsByProyekType->get($newbom->proyek_type_id, collect());
            
            [$groupedKomats, $groupProgress, $seniorPercentage, $materialOpened, $materialClosed] = $newbom->bomoneshow($documentsForProyekType, [], $listDataDocumentEncode);
            
            $groupBomNumberPercentage[$newbom->BOMnumber] = $seniorPercentage;
            $newbom->proyek_type = $projectTitle;
        }

        // Prepare the list of projects with their related newboms
        $revisiAll = [];

        foreach ($projects as $project) {
            $key = str_replace(' ', '_', $project->title);
            $revisiAll[$key]['boms'] = $groupedNewboms->get($project->id, collect())->all();
        }

        // Prepare and return the data
        $data = [
            'newboms' => $newboms,
            'revisiall' => $revisiAll,
            'groupbomnumberpercentage' => $groupBomNumberPercentage,
        ];

        return $data;
    }
    public static function historyPercentage()
    {
        // Retrieve all logs related to Newreport
        $logs = SystemLog::where('loggable_type', 'App\Models\Newbom')->get();
        return $logs;
    }

    public static function percentageandcount($listproject, $revisiall,$projects)
    {
        $newboms = Self::orderBy('created_at', 'desc')->get();
        $titles = $projects->pluck('id', 'title'); // Pluck title as the key and id as the value
        $documents = NewMemo::with(['feedbacks', 'komats', 'timelines'])->get();
        
        $additionalData = NewMemo::getAdditionalDataalldocumentdirect($documents);

        // Check if the additional data has the required keys
        if (!isset($additionalData['listdatadocuments']) || !isset($additionalData['percentagememoterbuka']) || !isset($additionalData['percentagememotertutup'])) {
            // Handle case where data is incomplete
            return response()->json(['error' => 'Data tidak lengkap'], 500);
        }

        foreach ($listproject as $keyan) {
            if ($keyan != "All") {
                // Get the project type ID
                $projectTypeId = $titles[$keyan] ?? null;
                
                if ($projectTypeId) {
                    $filterednewboms = $newboms->where('proyek_type_id', $projectTypeId);
                } else {
                    $filterednewboms = collect(); // Empty collection if project type not found
                }
            } else {
                $filterednewboms = $newboms;
            }

            // Initialize counts
            $persentaseterselesaikan = 0;
            $persentasetidakterselesaikan = 0;

            
            $listdatadocumentencode = $additionalData['listdatadocuments'];
            foreach($filterednewboms as $newbom){
                [$groupedKomats, $groupprogress, $seniorpercentage, $materialopened, $materialclosed] = $newbom->bomoneshow($documents, [], $listdatadocumentencode);
                $persentaseterselesaikan+= $seniorpercentage;
                $persentasetidakterselesaikan+= 100-$seniorpercentage;
            }


            // Store results in revisiall
            $revisiall[$keyan]['jumlahbom'] = [
                'terbuka' => $persentaseterselesaikan/count($filterednewboms),
                'tertutup' => $persentasetidakterselesaikan/count($filterednewboms)
            ];

            // Calculate percentages
            $totaldocument = $persentaseterselesaikan + $persentasetidakterselesaikan;
            $positifnewreport = $totaldocument > 0 ? ($persentaseterselesaikan / $totaldocument) * 100 : 0;

            $revisiall[$keyan]['persentasebom'] = [
                'terbuka' => $positifnewreport,
                'tertutup' => 100 - $positifnewreport
            ];
        }

        return $revisiall;
    }

}
