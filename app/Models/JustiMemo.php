<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JustiMemo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'justi_memos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'documentname',
        'documentnumber',
        'proyek_type_id',
        'documentstatus',
        'operator_id',
        'project_pic_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'project_pic_id' => 'array',  // Mengubah JSON menjadi array otomatis
    ];

    /**
     * Get the project type associated with the memo.
     */
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }

    /**
     * Get the operator associated with the memo.
     */
    public function operator()
    {
        return $this->belongsTo(Unit::class, 'operator_id');
    }

    /**
     * Get the units associated with the project PICs.
     */
    public function projectPics()
    {
        return Unit::whereIn('id', $this->project_pic_id)->get();
    }

    // Relasi dengan JustiMemosFeedback
    public function feedbacks()
    {
        return $this->hasMany(JustiMemoFeedback::class, 'justi_memo_id');
    }

    public function systemLogs()
    {
        return $this->morphMany(SystemLog::class, 'loggable');
    }
    public function notifsystem()
    {
        return $this->morphMany(Notification::class, 'notifmessage');
    }
    public function timelines()
    {
        return $this->hasMany(JustiMemoTimeline::class);
    }

    public function detailonedocument(){
        $operator=$this->operator;
        $temporaryrule="GUEST";              
        if(isset(auth()->user()->rule)){
            $temporaryrule=auth()->user()->rule;
        }
        $userinformations = $this->feedbacks;
        
        $MTPRsend= "Aktif"; // Tambah variabel baru
        $operatorsignature = "Nonaktif"; // Tambah variabel baru
        $operatorshare = "Nonaktif";
        $unitpicvalidation=[];
        $selfunitvalidation= "Nonaktif"; // Tambah variabel baru
        $unitvalidation = "Nonaktif"; // Tambah variabel baru
        
        $operatorcombinevalidation = "Nonaktif"; // Tambah variabel baru
        
        $manageroperatorvalidation = "Tidak Terlibat"; // Tambah variabel baru

        $seniormanagervalidation = "Nonaktif"; // Tambah variabel baru
        $MTPRvalidation= "Nonaktif"; // Tambah variabel baru
        
        
        $timelines = collect($this->timelines); // Menggunakan collect untuk $timelines
        $SMname="Belum ditentukan";
        $projectpics=[];
        
        $antarkondisi="";
        
        if (!empty($this->project_pic)) {
            $projectpics = json_decode($this->project_pic);
        }
        
        
        
        // Membuat list yang berisi array keseluruhan $projectpics dengan status nonaktif
        if (!empty($this->project_pic)) {
            foreach ($projectpics as $picname) {
                $lokalarray = [];
                $lokalarray[$picname] = "Nonaktif";
                $unitpicvalidation[$picname]= "Nonaktif";
                $arrayprojectpicscount[] = $lokalarray;
            }
        }
        
            
        foreach ($userinformations as $userinformation) {
            $picname = $userinformation->pic;
            $levelname = $userinformation->level;

            //operatorsignature
            if(in_array($picname, [$operator])&&in_array($levelname, ["signature"])){
                $operatorsignature = "Aktif";
            }

            //selfunitvalidation awal
            $statuspersetujuan_selfunitvalidation=["1"=>"Tidak","2"=>"Tidak",];
            if ($temporaryrule==$operator) {
                $conditionoffile2= $userinformation->conditionoffile2??"";
                if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $conditionoffile2 == "combine") {
                    $selfunitvalidation = "Aktif";
                }
                
            }
            $statuspersetujuan_selfunitvalidation["1"]="Ya";
            $conditionoffile = $userinformation->conditionoffile;
            if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& ($conditionoffile == "Approved" || $conditionoffile == "Approved by Manager")) {
                $statuspersetujuan_selfunitvalidation["2"]="Ya";
            }
            if($statuspersetujuan_selfunitvalidation["1"]=="Ya"&&$statuspersetujuan_selfunitvalidation["2"]=="Ya"){
                $selfunitvalidation = "Aktif";
            }
            //selfunitvalidation akhir


            //unitpicvalidation dan unitvalidation
            if (!empty($projectpics)) {
                $operatorshare = "Aktif";
                $statuspersetujuan_unitvalidation=["1"=>"Tidak","2"=>"Tidak"];
                $statuspersetujuan_unitvalidation["1"]="Ya";
                $nilaiinformasi = $userinformation->conditionoffile;
                if (in_array($picname, $projectpics) && ($nilaiinformasi == "Approved" || $nilaiinformasi == "Approved by Manager")) {
                    $statuspersetujuan_unitvalidation["2"] = "Ya";
                }       
                
                
                $nilaiinformasi  = $userinformation->conditionoffile2 ??'';
                if (in_array($picname, $projectpics) && $nilaiinformasi == "feedback") {
                    //jika pic ada maka otomatis status ongoing
                    $unitpicvalidation[$picname]= "Ongoing";
                }
                if($statuspersetujuan_unitvalidation["1"]=="Ya"&&$statuspersetujuan_unitvalidation["2"]=="Ya"){
                    $unitpicvalidation[$picname]= "Aktif";
                }
            }

            //operatorcombinevalidation
            if($picname==$operator){

                $nilaiinformasi = $userinformation->conditionoffile2;
                if ($nilaiinformasi == "combine") {
                    if($operatorcombinevalidation == "Nonaktif"){
                        $operatorcombinevalidation = "Ongoing";
                    }
                }
            }
            if(in_array($picname, [$operator])&&in_array($levelname, ["Manager ".$operator, "Senior Manager Desain", "Senior Manager Teknologi Produksi"])){
                $operatorcombinevalidation  = "Aktif";
            }

            //seniormanagervalidation
            if(in_array($levelname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"])){
                $seniormanagervalidation = 'Belum dibaca';
            }
            if(in_array($picname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi Produksi"])&&in_array($levelname, ["MTPR"])){
                $seniormanagervalidation = "Aktif";
            }
            if(in_array($picname, ["MTPR"])&&in_array($levelname, ["selesai"])){
                $MTPRvalidation = "Aktif";
            }

            //OPERATORACTIVATION
            if(in_array($levelname, ["Manager ".$operator])){
                $manageroperatorvalidation = "Terlibat";
                if($manageroperatorvalidation == "Terlibat"){
                    if($operator=="Product Engineering"){
                        $SMname="Senior Manager Engineering";
                    }elseif($operator=="Desain Elektrik"||$operator=="Desain Carbody"||$operator=="Desain Bogie & Wagon"||$operator=="Desain Mekanik & Interior"){
                        $SMname="Senior Manager Desain";
                    }elseif($operator=="Teknologi Proses"||$operator=="Shop Drawing"||$operator=="Welding Technology"||$operator=="Preparation & Support"){
                        $SMname="Senior Manager Teknologi Produksi";
                    }

                }
            }elseif(in_array($levelname, ["Senior Manager Desain"])){
                $SMname="Senior Manager Desain";
            }elseif(in_array($levelname, ["Senior Manager Teknologi Produksi"])){
                $SMname="Senior Manager Teknologi Produksi";
            }

            if($picname =="Manager ".$operator && $levelname==$SMname){
                $antarkondisi = "Aktif";
            }

        }

        $activeValidations = array_filter($unitpicvalidation, function($value) {
            return $value == "Aktif";
        });

        if (count($unitpicvalidation) == count($activeValidations)) {
            if(!empty($this->project_pic)) {
                $unitvalidation = "Aktif";
                if($unitvalidation == "Aktif") { // Perbaiki penugasan nilai di sini
                    if($operatorcombinevalidation== 'Nonaktif') {
                        $nama_divisi = $operator;
                        $timelineExist = $timelines->where('infostatus', $nama_divisi.'_combine'.'_read')
                        ->isNotEmpty();
                        if($timelineExist) {
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
            if($operatorshare == 'Nonaktif'){
                $operatorshare = 'Belum dibaca';

                $nama_divisi=$this->operator;

                $timelineExist = $timelines->where('infostatus', $nama_divisi.'_share'.'_read')
                ->isNotEmpty();

                if(($timelineExist)){
                    $operatorshare = 'Ongoing';  
                }
            }                    
        }
        if ($operatorshare == 'Aktif') {
            $completedSteps++;
            if(isset($projectpics)){
                if (!empty($this->project_pic)) {
                    foreach ($projectpics as $picname) {
                        if($unitpicvalidation[$picname]=="Nonaktif"){
                            $unitpicvalidation[$picname]= "Belum dibaca";
                            $timelineExist1 = $timelines->where('infostatus', $picname.'_unit'.'_read')
                            ->isNotEmpty();
                            if(($timelineExist1)){
                                $unitpicvalidation[$picname] = 'Sudah dibaca';  
                            }
                        }
                    }
                }
            }
        }

        if(isset($projectpics)){
            for ($u = 0; $u < count($projectpics); $u++) {
                $totalSteps++;
                if ($unitpicvalidation[$projectpics[$u]] == 'Aktif') {
                    $completedSteps++;
                }
            }
        }
        
        if ($operatorcombinevalidation == 'Aktif') {
            $completedSteps++;
            if($manageroperatorvalidation == "Tidak Terlibat"){
                if($seniormanagervalidation == 'Nonaktif'){
                    $seniormanagervalidation = 'Belum dibaca';
                    $nama_divisis=["Senior Manager Teknologi Produksi","Senior Manager Engineering","Senior Manager Desain",];
                    for ($k = 0; $k < count($nama_divisis); $k++) {
                        $timelineExist3= $timelines->where('infostatus', $nama_divisis[$k] . '_seniorvalid' . '_read')
                        ->isNotEmpty();
                        if(($timelineExist3)){
                            $seniormanagervalidation = "Ongoing";
                            break;
                        }
                    }
                }    
            }
            elseif($manageroperatorvalidation == "Terlibat"){
                $manageroperatorvalidation = 'Belum dibaca';  
                $timelineExist2 = $timelines->where('infostatus', "Manager ".$operator.'_unit'.'_read')
                ->isNotEmpty();
                if(($timelineExist2)){
                    $manageroperatorvalidation  = 'Sudah dibaca';  
                    if($antarkondisi == "Aktif"){
                        $manageroperatorvalidation  = "Aktif"; 
                    }
                    
                }
                if($manageroperatorvalidation == 'Aktif'){
                    if($seniormanagervalidation == 'Nonaktif'){
                        $seniormanagervalidation = 'Belum dibaca';
                        $nama_divisis=["Senior Manager Teknologi Produksi","Senior Manager Engineering","Senior Manager Desain",];
                        for ($k = 0; $k < count($nama_divisis); $k++) {
                            $timelineExist3= $timelines->where('infostatus', $nama_divisis[$k] . '_seniorvalid' . '_read')
                            ->isNotEmpty();
                            if(($timelineExist3)){
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
            if($MTPRvalidation=="Nonaktif"){
                $MTPRvalidation = 'Belum dibaca';
                $nama_divisi="MTPR";
                $timelineExist123= $timelines->where('infostatus', $nama_divisi.'_finish'.'_read')
                ->isNotEmpty();
                if(($timelineExist123)){
                    $MTPRvalidation = 'Ongoing';  
                }
            }
            
        }

        if ($MTPRvalidation == 'Aktif') {
            $completedSteps++;
        }
        
        
        $positionPercentage = intval(($completedSteps / $totalSteps) * 100); // Mengonversi ke integer
        if($this->documentstatus=="Tertutup"){
            if($MTPRvalidation!="Aktif"){
                $PEcombineworkstatus="Status belum didefenisikan";
            }
            // 'documentshared' memiliki nilai
            $positionPercentage=100;
            $MTPRsend= "Aktif"; // Tambah variabel baru
            $operatorshare = "Aktif"; // Tambah variabel baru
            if($manageroperatorvalidation = "Terlibat"){
                $manageroperatorvalidation = "Aktif";
            }
            $operatorcombinevalidation = "Aktif"; // Tambah variabel baru
            $seniormanagervalidation = "Aktif"; // Tambah variabel baru
            $MTPRvalidation= "Aktif"; // Tambah variabel baru
            $selfunitvalidation= "Aktif"; // Tambah variabel baru
            $unitvalidation = "Aktif"; // Tambah variabel baru
            $operatorsignature = "Aktif"; // Tambah variabel baru    
            if(isset($projectpics)){
                if (!empty($this->project_pic)) {
                    foreach ($projectpics as $picname) {
                        $unitpicvalidation[$picname]= "Aktif";
                    }
                }
            }
        }

        $posisi1="on";
        $posisi2="off";
        $posisi3="off";
        if($operatorshare=="Aktif"){
            $posisi1="off";
            $posisi2="on";
            $posisi3="off";
        }
            

        if($unitvalidation=="Aktif"){
            $posisi1="off";
            $posisi2="off";
            $posisi3="on";
        }


        return [$MTPRsend,$operatorsignature,$operatorshare,$unitpicvalidation,$unitvalidation,$operatorcombinevalidation,$selfunitvalidation,$seniormanagervalidation,$MTPRvalidation,$manageroperatorvalidation,$posisi1,$posisi2,$posisi3,$positionPercentage,$SMname];
        
    }

    public static function indexlogic($listproject) {
        // Mendapatkan semua dokumen dengan relasi terkait
        $documents = NewMemo::with(['feedbacks', 'komats', 'timelines'])->get();
        $units = Unit::all();
        
        // Mengelompokkan dokumen berdasarkan status
        $openDocuments = $documents->filter(function($doc) {
            return $doc->documentstatus === 'Terbuka';
        });
    
        $otherDocuments = $documents->filter(function($doc) {
            return $doc->documentstatus !== 'Terbuka';
        });
    
        // Mengurutkan dokumen berdasarkan created_at
        $openDocuments = $openDocuments->sortByDesc('created_at');
        $otherDocuments = $otherDocuments->sortByDesc('created_at');
    
        // Menggabungkan dokumen, dengan dokumen "Terbuka" di bagian atas
        $documents = $openDocuments->concat($otherDocuments);
        
        // Mendapatkan detail dari setiap dokumen
        foreach ($documents as $document) {
            list(
                $MTPRsend, $operatorsignature, $operatorshare, $unitpicvalidation, $unitvalidation, 
                $operatorcombinevalidation, $selfunitvalidation, $seniormanagervalidation, $MTPRvalidation, 
                $manageroperatorvalidation, $posisi1, $posisi2, $posisi3, $positionPercentage, $SMname
            ) = $document->detailonedocument();
    
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
        }
        
        // Mengelompokkan dokumen berdasarkan tipe proyek dan unit
        $revisiall = [];
        foreach ($listproject as $project) {
            $key = str_replace(' ', '_', $project->title);
            
            // Mengelompokkan dokumen berdasarkan proyek_type_id
            $projectDocuments = $documents->where('proyek_type_id', $project->id);
    
            // Menyimpan dokumen yang sesuai dengan proyek
            $revisiall[$key]['documents'] = $projectDocuments->values()->all();
    
            foreach ($units as $unit) {
                // Mengelompokkan dokumen berdasarkan unit dan proyek_type_id
                $unitDocuments = $projectDocuments->filter(function($doc) use ($unit) {
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
            $unboundDocuments = $projectDocuments->filter(function($doc) {
                return is_null(json_decode($doc->project_pic, true));
            });
    
            $revisiall[$key]['units']['unbound'] = $unboundDocuments->values()->all();
        }
    
        return [$revisiall, $documents];
    }
   

}
