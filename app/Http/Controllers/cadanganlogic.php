private function getAdditionalDataonedocument($iddocument){
$temporaryrule="GUEST";

if(isset(auth()->user()->rule)){
$temporaryrule=auth()->user()->rule;
}
$document = Dokumen::findOrFail($iddocument);
$informasidokumen=[];
$informasidokumen['documentname']=$document->documentname;
$informasidokumen['documentnumber']=$document->documentnumber;
$informasidokumen['memokind']=$document->memokind;
$informasidokumen['memoorigin']=$document->memoorigin;
$informasidokumen['documentstatus']=$document->documentstatus;
$informasidokumen['category']=$document->category;
$informasidokumenencoded=json_encode($informasidokumen);
$timeline = json_decode($document->timeline, true);



$userinformations = json_decode($document->userinformations);
$status = "";
$indonesiatimestamps = [];
$level = '';
$MTPRsend= "Aktif"; // Tambah variabel baru
$PEshare = "Nonaktif";
$PEmanagervalidation = "Nonaktif"; // Tambah variabel baru
$seniormanagervalidation = "Nonaktif"; // Tambah variabel baru
$MTPRvalidation= "Nonaktif"; // Tambah variabel baru
$MPEvalidation = "Nonaktif"; // Tambah variabel baru
$selfunitvalidation= "Nonaktif"; // Tambah variabel baru
$unitvalidation = "Nonaktif"; // Tambah variabel baru
$PEsignature = "Nonaktif"; // Tambah variabel baru

$projectpics = json_decode($document->project_pic);
$arrayprojectpicscount = [];
$unitpicvalidation=[];

// Membuat list yang berisi array keseluruhan $projectpics dengan status nonaktif
if ($projectpics !== null) {
foreach ($projectpics as $picname) {
$lokalarray = [];
$lokalarray[$picname] = "Nonaktif";
$unitpicvalidation[$picname]= "Nonaktif";
$arrayprojectpicscount[] = $lokalarray;
}
}


for ($i = 0; $i < count($userinformations); $i++) { $cekinformasiuser=json_decode($userinformations[$i])??[];
    if($cekinformasiuser!="" ){ $data=$cekinformasiuser; $picname=$data->pic;
    $levelname = $data->level;


    $sumberinformasi = $cekinformasiuser->userinformations;
    $userInfo = json_decode($sumberinformasi, true);
    //unitvalidation
    if ($projectpics !== null) {
    $PEshare = "Aktif";
    $statuspersetujuan_unitvalidation=[
    "1"=>"Tidak",
    "2"=>"Tidak",];
    $statuspersetujuan_unitvalidation["1"]="Ya";
    $nilaiinformasi = $userInfo['conditionoffile'];
    if (in_array($picname, $projectpics) && $nilaiinformasi == "Approved") {
    $statuspersetujuan_unitvalidation["2"]="Ya";
    }
    $nilaiinformasi = $userInfo['conditionoffile2']??'';
    if (in_array($picname, $projectpics) && $nilaiinformasi == "feedback") {
    //jika pic ada maka otomatis status ongoing
    $unitpicvalidation[$picname]= "Ongoing";
    }
    if($statuspersetujuan_unitvalidation["1"]=="Ya"&&$statuspersetujuan_unitvalidation["2"]=="Ya"){
    $unitpicvalidation[$picname]= "Aktif";
    }
    }
    //selfunitvalidation awal
    $statuspersetujuan_selfunitvalidation=["1"=>"Tidak","2"=>"Tidak",];
    if ($temporaryrule=="Product Engineering") {
    $conditionoffile2= $userInfo['conditionoffile2'];
    if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $conditionoffile2 == "combine") {
    $selfunitvalidation = "Aktif";
    break;
    }

    }
    $statuspersetujuan_selfunitvalidation["1"]="Ya";
    $conditionoffile = $userInfo['conditionoffile'];;
    if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $conditionoffile == "Approved") {
    $statuspersetujuan_selfunitvalidation["2"]="Ya";
    }
    if($statuspersetujuan_selfunitvalidation["1"]=="Ya"&&$statuspersetujuan_selfunitvalidation["2"]=="Ya"){
    $selfunitvalidation = "Aktif";
    }
    //selfunitvalidation akhir



    // if ($sumberinformasi) {
    // try {
    // $userInfo = json_decode($sumberinformasi, true);

    // // Check for JSON decoding errors
    // if (json_last_error() != JSON_ERROR_NONE) {
    // throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
    // }
    // } catch (\Exception $e) {
    // // Log the error message or throw an exception
    // \Log::error('Error decoding JSON: ' . $e->getMessage());

    // // Handle decoding error, for example:
    // $userInfo = [];
    // }

    // // //Unitvalidation
    // if ($projectpics !== null) {
    // $PEshare = "Aktif";
    // $statuspersetujuan=[
    // "1"=>"Tidak",
    // "2"=>"Tidak",];
    // foreach ($userInfo as $key => $value) {
    // // if ($key == 'hasilreview') {
    // // $nilaiinformasi = $value;
    // // if (in_array($picname, $projectpics) && $nilaiinformasi == "Ya, dapat diterima") {
    // // $statuspersetujuan["1"]="Ya";
    // // }
    // // }
    // $statuspersetujuan["1"]="Ya";
    // if ($key == 'conditionoffile') {
    // $nilaiinformasi = $value;
    // if (in_array($picname, $projectpics) && $nilaiinformasi == "Approved") {
    // $statuspersetujuan["2"]="Ya";
    // }
    // }
    // if ($key == 'conditionoffile2') {
    // $nilaiinformasi = $value;
    // if (in_array($picname, $projectpics) && $nilaiinformasi == "feedback") {
    // //jika pic ada maka otomatis status ongoing
    // $unitpicvalidation[$picname]= "Ongoing";
    // }
    // }
    // if($statuspersetujuan["1"]=="Ya"&&$statuspersetujuan["2"]=="Ya")
    // {
    // $unitpicvalidation[$picname]= "Aktif";

    // }
    // }
    // }


    // // //selfunitvalidation
    // $statuspersetujuan=["1"=>"Tidak","2"=>"Tidak",];
    // foreach ($userInfo as $key => $value) {
    // if ($key == 'conditionoffile2'&&$temporaryrule=="Product Engineering") {
    // $nilaiinformasi = $value;
    // if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $nilaiinformasi == "combine") {
    // $selfunitvalidation = "Aktif";
    // break;
    // }

    // }
    // // if ($key == 'hasilreview') {
    // // $nilaiinformasi = $value;
    // // if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $nilaiinformasi == "Ya, dapat diterima") {
    // // $statuspersetujuan["1"]="Ya";
    // // }
    // // }
    // $statuspersetujuan["1"]="Ya";
    // if ($key == 'conditionoffile') {
    // $nilaiinformasi = $value;
    // if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $nilaiinformasi == "Approved") {
    // $statuspersetujuan["2"]="Ya";
    // }
    // }
    // if($statuspersetujuan["1"]=="Ya"&&$statuspersetujuan["2"]=="Ya")
    // {$selfunitvalidation = "Aktif";
    // break;
    // }
    // }
    // }





    //PEsignature
    if(in_array($picname, ["Product Engineering"])&&in_array($levelname, ["signature"])){
    $PEsignature = "Aktif";
    }


    //PEmanagervalidation
    foreach ($userInfo as $key => $value) {
    $statuspersetujuan["1"]="Ya";
    if($picname=="Product Engineering"){
    if ($key == 'conditionoffile2') {
    $nilaiinformasi = $value;
    if ($nilaiinformasi == "combine") {
    if($PEmanagervalidation == "Nonaktif"){
    $PEmanagervalidation = "Ongoing";
    }
    }
    }
    }


    }
    if(in_array($picname, ["Product Engineering"])&&in_array($levelname, ["Manager Product Engineering", "Senior Manager
    Desain", "Senior Manager Teknologi Produksi"])){
    $PEmanagervalidation = "Aktif";
    }

    //seniormanagervalidation
    if(in_array($picname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi
    Produksi"])&&in_array($levelname, ["MTPR"])){
    $seniormanagervalidation = "Aktif";
    }

    //MPEvalidation
    if(in_array($picname, ["Manager Product Engineering"])&&in_array($levelname, ["Senior Manager Engineering"])){
    $MPEvalidation = "Aktif";
    }

    //MTPRvalidation
    if(in_array($picname, ["MTPR"])&&in_array($levelname, ["selesai"])){
    $MTPRvalidation = "Aktif";
    }

    }
    }


    // Check apakah semua unitpicvalidation adalah "Aktif" menggunakan array_filter()
    $activeValidations = array_filter($unitpicvalidation, function($value) {
    return $value == "Aktif";
    });

    if (count($unitpicvalidation) == count($activeValidations)) {
    if(isset($projectpics)) {
    $unitvalidation = "Aktif";
    if($unitvalidation == "Aktif") { // Perbaiki penugasan nilai di sini
    if($PEmanagervalidation == 'Nonaktif') {
    $nama_divisi = "Product Engineering";
    if(isset($timeline[$nama_divisi.'_combine'.'_read'])) {
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
    $file = TugasDivisi::whereRaw('CAST(iddocument AS CHAR) = ?', [$document->id])
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
    if($PEshare == 'Nonaktif'){
    $PEshare = 'Belum dibaca';
    $nama_divisi="Product Engineering";
    if(isset($timeline[$nama_divisi.'_share'.'_read'])){
    $PEshare = 'Ongoing';
    }
    }
    }
    if ($PEshare == 'Aktif') {
    $completedSteps++;
    if(isset($projectpics)){
    for ($u = 0; $u < count($projectpics); $u++) { if ($unitpicvalidation[$projectpics[$u]]=='Nonaktif' ) {
        $unitpicvalidation[$projectpics[$u]]='Belum dibaca' ; $nama_divisi=$projectpics[$u];
        if(isset($timeline[$nama_divisi.'_unit'.'_read'])){ $unitpicvalidation[$projectpics[$u]]='Sudah dibaca' ; } } }
        } } if(isset($projectpics)){ for ($u=0; $u < count($projectpics); $u++) { $totalSteps++; if
        ($unitpicvalidation[$projectpics[$u]]=='Aktif' ) { $completedSteps++; } } } if ($PEmanagervalidation=='Aktif' )
        { $completedSteps++; if($seniormanagervalidation=="Nonaktif" ){ $seniormanagervalidation="Ongoing" ; } } if
        ($seniormanagervalidation=='Aktif' ) { $completedSteps++; if($MTPRvalidation=="Nonaktif" ){
        $MTPRvalidation="Ongoing" ; } } if ($MTPRvalidation=='Aktif' ) { $completedSteps++; }
        $positionPercentage=intval(($completedSteps / $totalSteps) * 100); // Mengonversi ke integer $listdata=[];
        $userinformations=json_decode($document->userinformations, true);
        $count = count($userinformations);

        for ($su = 0; $su < $count; $su++) { $datadikirim=[]; $ringkasan=(json_decode($document->
            userinformations,true)[$su]);
            $datalokal=[];
            $sumberdata=json_decode(json_decode($ringkasan)->userinformations);
            $datadikirim['pic']=json_decode($ringkasan)->pic;
            $datadikirim['level']=json_decode($ringkasan)->level;
            $datalokal['nama penulis'] = isset($sumberdata->{'nama penulis'}) ? $sumberdata->{'nama penulis'} :
            $sumberdata->{'nama'};
            $datalokal['email'] = isset($sumberdata->{'email'}) ? $sumberdata->{'email'} : null;
            $datalokal['conditionoffile'] = isset($sumberdata->{'conditionoffile'}) ? $sumberdata->{'conditionoffile'} :
            null;
            $datalokal['conditionoffile2'] = isset($sumberdata->{'conditionoffile2'}) ?
            $sumberdata->{'conditionoffile2'} : null;
            $datalokal['hasilreview'] = isset($sumberdata->{'hasilreview'}) ? $sumberdata->{'hasilreview'} : null;
            $datalokal['sudahdibaca'] = isset($sumberdata->{'sudahdibaca'}) ? $sumberdata->{'sudahdibaca'} : null;
            $datalokal['listfilenames']=isset(json_decode($ringkasan)->listfilenames) ?
            json_decode($ringkasan)->listfilenames : [];
            $datalokal['listmetadatas']=isset(json_decode($ringkasan)->listmetadatas) ?
            json_decode($ringkasan)->listmetadatas : [];
            $datalokal['listlinkfiles']=isset(json_decode($ringkasan)->listlinkfiles) ?
            json_decode($ringkasan)->listlinkfiles : [];
            $datadikirim['userinformations']=$datalokal;
            $listdata[]=$datadikirim;
            }
            $datadikirimencoded=json_encode($listdata);
            return
            [$informasidokumenencoded,$datadikirimencoded,$positionPercentage,$unitpicvalidation,$projectpics,$PEsignature,
            $userinformations,$selfunitvalidation,$PEmanagervalidation,$unitvalidation,$status,$indonesiatimestamps,$level,$MTPRsend,$PEshare,$seniormanagervalidation,$MTPRvalidation,$MPEvalidation,$arrayprojectpicscount];

            }

            private function getAdditionalDataonedocument($iddocument){
            $temporaryrule="GUEST";

            if(isset(auth()->user()->rule)){
            $temporaryrule=auth()->user()->rule;
            }
            $document = Dokumen::findOrFail($iddocument);
            $informasidokumen=[];
            $informasidokumen['documentname']=$document->documentname;
            $informasidokumen['documentnumber']=$document->documentnumber;
            $informasidokumen['memokind']=$document->memokind;
            $informasidokumen['memoorigin']=$document->memoorigin;
            $informasidokumen['documentstatus']=$document->documentstatus;
            $informasidokumen['category']=$document->category;
            $informasidokumenencoded=json_encode($informasidokumen);
            $userinformations = json_decode($document->userinformations);



            $status = "";
            $indonesiatimestamps = [];
            $level = '';
            $MTPRsend= "Aktif"; // Tambah variabel baru
            $PEshare = "Nonaktif";
            $PEmanagervalidation = "Nonaktif"; // Tambah variabel baru
            $seniormanagervalidation = "Nonaktif"; // Tambah variabel baru
            $MTPRvalidation= "Nonaktif"; // Tambah variabel baru
            $MPEvalidation = "Nonaktif"; // Tambah variabel baru
            $selfunitvalidation= "Nonaktif"; // Tambah variabel baru
            $unitvalidation = "Nonaktif"; // Tambah variabel baru
            $PEsignature = "Nonaktif"; // Tambah variabel baru
            $projectpics=[];
            if(isset($projectpics)){
            $projectpics = json_decode($document->project_pic);
            }

            $arrayprojectpicscount = [];
            $unitpicvalidation=[];

            // Membuat list yang berisi array keseluruhan $projectpics dengan status nonaktif
            if(isset($projectpics)) {
            foreach ($projectpics as $picname) {
            $lokalarray = [];
            $lokalarray[$picname] = "Nonaktif";
            $unitpicvalidation[$picname]= "Nonaktif";
            $arrayprojectpicscount[] = $lokalarray;
            }
            }


            for ($i = 0; $i < count($userinformations); $i++) {
                $cekinformasiuser=json_decode($userinformations[$i])??[]; if($cekinformasiuser!="" ){
                $data=$cekinformasiuser; $picname=$data->pic;
                $levelname = $data->level;
                $sumberinformasi = $cekinformasiuser->userinformations;
                $userInfo = json_decode($sumberinformasi, true);

                //unitvalidation
                if ($projectpics !== null) {
                $PEshare = "Aktif";
                $statuspersetujuan_unitvalidation=[
                "1"=>"Tidak",
                "2"=>"Tidak",];
                $statuspersetujuan_unitvalidation["1"]="Ya";
                $nilaiinformasi = $userInfo['conditionoffile'];
                if (in_array($picname, $projectpics) && $nilaiinformasi == "Approved") {
                $statuspersetujuan_unitvalidation["2"]="Ya";
                }
                $nilaiinformasi = $userInfo['conditionoffile2']??'';
                if (in_array($picname, $projectpics) && $nilaiinformasi == "feedback") {
                //jika pic ada maka otomatis status ongoing
                $unitpicvalidation[$picname]= "Ongoing";
                }
                if($statuspersetujuan_unitvalidation["1"]=="Ya"&&$statuspersetujuan_unitvalidation["2"]=="Ya"){
                $unitpicvalidation[$picname]= "Aktif";
                }
                }


                //selfunitvalidation awal
                $statuspersetujuan_selfunitvalidation=["1"=>"Tidak","2"=>"Tidak",];
                if ($temporaryrule=="Product Engineering") {
                $conditionoffile2= $userInfo['conditionoffile2']??'';
                if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $conditionoffile2 == "combine") {
                $selfunitvalidation = "Aktif";
                break;
                }

                }
                $statuspersetujuan_selfunitvalidation["1"]="Ya";
                $conditionoffile = $userInfo['conditionoffile'];;
                if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $conditionoffile == "Approved") {
                $statuspersetujuan_selfunitvalidation["2"]="Ya";
                }
                if($statuspersetujuan_selfunitvalidation["1"]=="Ya"&&$statuspersetujuan_selfunitvalidation["2"]=="Ya"){
                $selfunitvalidation = "Aktif";
                }
                //selfunitvalidation akhir




                //PEsignature
                if(in_array($picname, ["Product Engineering"])&&in_array($levelname, ["signature"])){
                $PEsignature = "Aktif";
                }


                //PEmanagervalidation
                foreach ($userInfo as $key => $value) {
                $statuspersetujuan["1"]="Ya";
                if($picname=="Product Engineering"){
                if ($key == 'conditionoffile2') {
                $nilaiinformasi = $value;
                if ($nilaiinformasi == "combine") {
                if($PEmanagervalidation == "Nonaktif"){
                $PEmanagervalidation = "Ongoing";
                }
                }
                }
                }


                }
                if(in_array($picname, ["Product Engineering"])&&in_array($levelname, ["Manager Product Engineering",
                "Senior Manager Desain", "Senior Manager Teknologi Produksi"])){
                $PEmanagervalidation = "Aktif";
                }

                //seniormanagervalidation
                if(in_array($picname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior Manager Teknologi
                Produksi"])&&in_array($levelname, ["MTPR"])){
                $seniormanagervalidation = "Aktif";
                }

                //MPEvalidation
                if(in_array($picname, ["Manager Product Engineering"])&&in_array($levelname, ["Senior Manager
                Engineering"])){
                $MPEvalidation = "Aktif";
                }

                //MTPRvalidation
                if(in_array($picname, ["MTPR"])&&in_array($levelname, ["selesai"])){
                $MTPRvalidation = "Aktif";
                }

                }
                }


                // Check apakah semua unitpicvalidation adalah "Aktif" menggunakan array_filter()
                $activeValidations = array_filter($unitpicvalidation, function($value) {
                return $value == "Aktif";
                });

                if (count($unitpicvalidation) == count($activeValidations)) {
                if(isset($projectpics)){
                $unitvalidation = "Aktif";
                if($unitvalidation = "Aktif"){
                if($PEmanagervalidation == 'Nonaktif'){
                $PEmanagervalidation = 'Belum dibaca';
                $nama_divisi="Product Engineering";
                if(isset($timeline[$nama_divisi.'_combine'.'_read'])){
                $PEmanagervalidation = 'Sudah dibaca';
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
                $file = TugasDivisi::whereRaw('CAST(iddocument AS CHAR) = ?', [$document->id])
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
                $timeline = json_decode($document->timeline, true);
                if ($MTPRsend == 'Aktif') {
                $completedSteps++;
                if($PEshare == 'Nonaktif'){
                $PEshare = 'Belum dibaca';
                $nama_divisi="Product Engineering";
                if(isset($timeline[$nama_divisi.'_share'.'_read'])){
                $PEshare = 'Ongoing';
                }
                }
                }
                if ($PEshare == 'Aktif') {
                $completedSteps++;
                if(isset($projectpics)){
                for ($u = 0; $u < count($projectpics); $u++) { $totalSteps++; if
                    ($unitpicvalidation[$projectpics[$u]]=='Aktif' ) { $completedSteps++; } if
                    ($unitpicvalidation[$projectpics[$u]]=='Nonaktif' ) {
                    $unitpicvalidation[$projectpics[$u]]='Belum dibaca' ; $nama_divisi=$projectpics[$u];
                    if(isset($timeline[$nama_divisi.'_unit'.'_read'])){
                    $unitpicvalidation[$projectpics[$u]]='Sudah dibaca' ; } } } } } if ($PEmanagervalidation=='Aktif' )
                    { $completedSteps++; if($seniormanagervalidation=="Nonaktif" ){ $seniormanagervalidation="Ongoing" ;
                    } } if ($seniormanagervalidation=='Aktif' ) { $completedSteps++; if($MTPRvalidation=="Nonaktif" ){
                    $MTPRvalidation="Ongoing" ; } } if ($MTPRvalidation=='Aktif' ) { $completedSteps++; }
                    $positionPercentage=intval(($completedSteps / $totalSteps) * 100); // Mengonversi ke integer
                    $listdata=[]; $userinformations=json_decode($document->userinformations, true);
                    $count = count($userinformations);

                    for ($su = 0; $su < $count; $su++) { $datadikirim=[]; $ringkasan=(json_decode($document->
                        userinformations,true)[$su]);
                        $datalokal=[];
                        $sumberdata=json_decode(json_decode($ringkasan)->userinformations);
                        $datadikirim['pic']=json_decode($ringkasan)->pic;
                        $datadikirim['level']=json_decode($ringkasan)->level;
                        $datalokal['nama penulis'] = isset($sumberdata->{'nama penulis'}) ? $sumberdata->{'nama
                        penulis'} : $sumberdata->{'nama'};
                        $datalokal['email'] = isset($sumberdata->{'email'}) ? $sumberdata->{'email'} : null;
                        $datalokal['conditionoffile'] = isset($sumberdata->{'conditionoffile'}) ?
                        $sumberdata->{'conditionoffile'} : null;
                        $datalokal['conditionoffile2'] = isset($sumberdata->{'conditionoffile2'}) ?
                        $sumberdata->{'conditionoffile2'} : null;
                        $datalokal['hasilreview'] = isset($sumberdata->{'hasilreview'}) ? $sumberdata->{'hasilreview'} :
                        null;
                        $datalokal['sudahdibaca'] = isset($sumberdata->{'sudahdibaca'}) ? $sumberdata->{'sudahdibaca'} :
                        null;
                        $datalokal['listfilenames']=isset(json_decode($ringkasan)->listfilenames) ?
                        json_decode($ringkasan)->listfilenames : [];
                        $datalokal['listmetadatas']=isset(json_decode($ringkasan)->listmetadatas) ?
                        json_decode($ringkasan)->listmetadatas : [];
                        $datalokal['listlinkfiles']=isset(json_decode($ringkasan)->listlinkfiles) ?
                        json_decode($ringkasan)->listlinkfiles : [];
                        $datadikirim['userinformations']=$datalokal;
                        $listdata[]=$datadikirim;
                        }
                        $datadikirimencoded=json_encode($listdata);
                        return
                        [$informasidokumenencoded,$datadikirimencoded,$positionPercentage,$unitpicvalidation,$projectpics,$PEsignature,
                        $userinformations,$selfunitvalidation,$PEmanagervalidation,$unitvalidation,$status,$indonesiatimestamps,$level,$MTPRsend,$PEshare,$seniormanagervalidation,$MTPRvalidation,$MPEvalidation,$arrayprojectpicscount];

                        }

                        private function getAdditionalDataonedocumentcadangan($iddocument){
                        $temporaryrule="GUEST";

                        if(isset(auth()->user()->rule)){
                        $temporaryrule=auth()->user()->rule;
                        }
                        $document = Dokumen::findOrFail($iddocument);
                        $informasidokumen=[];
                        $informasidokumen['documentname']=$document->documentname;
                        $informasidokumen['documentnumber']=$document->documentnumber;
                        $informasidokumen['memokind']=$document->memokind;
                        $informasidokumen['memoorigin']=$document->memoorigin;
                        $informasidokumen['documentstatus']=$document->documentstatus;
                        $informasidokumen['category']=$document->category;
                        $informasidokumenencoded=json_encode($informasidokumen);









                        $userinformations = json_decode($document->userinformations);
                        $status = "";
                        $indonesiatimestamps = [];
                        $level = '';
                        $MTPRsend= "Aktif"; // Tambah variabel baru
                        $PEshare = "Nonaktif";
                        $PEmanagervalidation = "Nonaktif"; // Tambah variabel baru
                        $seniormanagervalidation = "Nonaktif"; // Tambah variabel baru
                        $MTPRvalidation= "Nonaktif"; // Tambah variabel baru
                        $MPEvalidation = "Nonaktif"; // Tambah variabel baru
                        $selfunitvalidation= "Nonaktif"; // Tambah variabel baru
                        $unitvalidation = "Nonaktif"; // Tambah variabel baru
                        $PEsignature = "Nonaktif"; // Tambah variabel baru
                        $projectpics = json_decode($document->project_pic);
                        $arrayprojectpicscount = [];
                        $unitpicvalidation=[];

                        // Membuat list yang berisi array keseluruhan $projectpics dengan status nonaktif
                        if ($projectpics !== null) {
                        foreach ($projectpics as $picname) {
                        $lokalarray = [];
                        $lokalarray[$picname] = "Nonaktif";
                        $unitpicvalidation[$picname]= "Nonaktif";
                        $arrayprojectpicscount[] = $lokalarray;
                        }
                        }
                        // Periksa apakah $document->project_pic memiliki nilai
                        if ($projectpics !== null) {
                        $PEshare = "Aktif";
                        for ($i = 0; $i < count($userinformations); $i++) { if(json_decode($userinformations[$i])!="" ){
                            $picname=json_decode($userinformations[$i])->pic;

                            $sumberinformasi = json_decode($userinformations[$i])->userinformations;
                            if ($sumberinformasi) {
                            try {
                            $userInfo = json_decode($sumberinformasi, true);

                            // Check for JSON decoding errors
                            if (json_last_error() != JSON_ERROR_NONE) {
                            throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                            }
                            } catch (\Exception $e) {
                            // Log the error message or throw an exception
                            \Log::error('Error decoding JSON: ' . $e->getMessage());

                            // Handle decoding error, for example:
                            $userInfo = [];
                            }
                            $statuspersetujuan=[
                            "1"=>"Tidak",
                            "2"=>"Tidak",];
                            foreach ($userInfo as $key => $value) {
                            // if ($key == 'hasilreview') {
                            // $nilaiinformasi = $value;
                            // if (in_array($picname, $projectpics) && $nilaiinformasi == "Ya, dapat diterima") {
                            // $statuspersetujuan["1"]="Ya";
                            // }
                            // }
                            $statuspersetujuan["1"]="Ya";
                            if ($key == 'conditionoffile') {
                            $nilaiinformasi = $value;
                            if (in_array($picname, $projectpics) && $nilaiinformasi == "Approved") {
                            $statuspersetujuan["2"]="Ya";
                            }
                            }
                            if ($key == 'conditionoffile2') {
                            $nilaiinformasi = $value;
                            if (in_array($picname, $projectpics) && $nilaiinformasi == "feedback") {
                            //jika pic ada maka otomatis status ongoing
                            $unitpicvalidation[$picname]= "Ongoing";
                            }
                            }
                            if($statuspersetujuan["1"]=="Ya"&&$statuspersetujuan["2"]=="Ya")
                            {
                            $unitpicvalidation[$picname]= "Aktif";

                            }
                            }
                            }
                            }
                            }
                            // Check apakah semua unitpicvalidation adalah "Aktif" menggunakan array_filter()
                            $activeValidations = array_filter($unitpicvalidation, function($value) {
                            return $value == "Aktif";
                            });

                            if (count($unitpicvalidation) == count($activeValidations)) {
                            $unitvalidation = "Aktif";

                            // Periksa apakah pengguna adalah PE dan pereadstatus adalah null
                            if ($temporaryrule != "Product Engineering" && is_null($document->pereadstatus)) {
                            $document->update([
                            'pereadstatus' => now(),
                            ]);
                            // Cari tugas divisi yang pertama ditemukan
                            $file = TugasDivisi::whereRaw('CAST(iddocument AS CHAR) = ?', [$document->id])
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




                            }
                            for ($i = 0; $i < count($userinformations); $i++) {
                                if(json_decode($userinformations[$i])!="" ){
                                $picname=json_decode($userinformations[$i])->pic;
                                $levelname = json_decode($userinformations[$i])->level;
                                $sumberinformasi = json_decode($userinformations[$i])->userinformations;
                                if ($sumberinformasi) {
                                try {
                                $userInfo = json_decode($sumberinformasi, true);

                                // Check for JSON decoding errors
                                if (json_last_error() != JSON_ERROR_NONE) {
                                throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                                }
                                } catch (\Exception $e) {
                                // Log the error message or throw an exception
                                \Log::error('Error decoding JSON: ' . $e->getMessage());

                                // Handle decoding error, for example:
                                $userInfo = [];
                                }
                                $statuspersetujuan=[
                                "1"=>"Tidak",
                                "2"=>"Tidak",];
                                foreach ($userInfo as $key => $value) {
                                if ($key == 'conditionoffile2'&&$temporaryrule=="Product Engineering") {
                                $nilaiinformasi = $value;
                                if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $nilaiinformasi ==
                                "combine") {
                                $selfunitvalidation = "Aktif";
                                break;
                                }

                                }
                                // if ($key == 'hasilreview') {
                                // $nilaiinformasi = $value;
                                // if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $nilaiinformasi ==
                                "Ya, dapat diterima") {
                                // $statuspersetujuan["1"]="Ya";
                                // }
                                // }
                                $statuspersetujuan["1"]="Ya";
                                if ($key == 'conditionoffile') {
                                $nilaiinformasi = $value;
                                if (($picname==$temporaryrule ||$levelname==$temporaryrule )&& $nilaiinformasi ==
                                "Approved") {
                                $statuspersetujuan["2"]="Ya";
                                }
                                }
                                if($statuspersetujuan["1"]=="Ya"&&$statuspersetujuan["2"]=="Ya")
                                {$selfunitvalidation = "Aktif";
                                break;
                                }
                                }
                                }
                                }
                                }
                                for ($i = 0; $i < count($userinformations); $i++) {
                                    if(json_decode($userinformations[$i])!="" ){
                                    $picname=json_decode($userinformations[$i])->pic;
                                    $levelname = json_decode($userinformations[$i])->level;


                                    if(
                                    in_array($picname, ["Senior Manager Engineering", "Senior Manager Desain", "Senior
                                    Manager Teknologi Produksi"])&&in_array($levelname, ["MTPR"])
                                    ){
                                    $seniormanagervalidation = "Aktif";
                                    break;
                                    }

                                    }
                                    }
                                    for ($i = 0; $i < count($userinformations); $i++) {
                                        if(json_decode($userinformations[$i])!="" ){
                                        $picname=json_decode($userinformations[$i])->pic;
                                        $levelname = json_decode($userinformations[$i])->level;
                                        if(in_array($picname, ["Product Engineering"])&&in_array($levelname,
                                        ["signature"])){
                                        $PEsignature = "Aktif";
                                        break;
                                        }
                                        }
                                        }
                                        for ($i = 0; $i < count($userinformations); $i++) {
                                            if(json_decode($userinformations[$i])!="" ){
                                            $picname=json_decode($userinformations[$i])->pic;
                                            $levelname = json_decode($userinformations[$i])->level;



                                            //tambahakn progress oranye
                                            $sumberinformasi = json_decode($userinformations[$i])->userinformations;
                                            if ($sumberinformasi) {
                                            try {
                                            $userInfo = json_decode($sumberinformasi, true);

                                            // Check for JSON decoding errors
                                            if (json_last_error() != JSON_ERROR_NONE) {
                                            throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
                                            }
                                            } catch (\Exception $e) {
                                            // Log the error message or throw an exception
                                            \Log::error('Error decoding JSON: ' . $e->getMessage());

                                            // Handle decoding error, for example:
                                            $userInfo = [];
                                            }
                                            foreach ($userInfo as $key => $value) {
                                            $statuspersetujuan["1"]="Ya";
                                            if($picname=="Product Engineering"){
                                            if ($key == 'conditionoffile2') {
                                            $nilaiinformasi = $value;
                                            if ($nilaiinformasi == "combine") {
                                            if($PEmanagervalidation == "Nonaktif"){
                                            $PEmanagervalidation = "Ongoing";
                                            }
                                            }
                                            }
                                            }


                                            }
                                            }
                                            //tambahakn progress oranye


                                            if(in_array($picname, ["Product Engineering"])&&in_array($levelname,
                                            ["Manager Product Engineering", "Senior Manager Desain", "Senior Manager
                                            Teknologi Produksi"])){
                                            $PEmanagervalidation = "Aktif";
                                            break;
                                            }
                                            }
                                            }
                                            for ($i = 0; $i < count($userinformations); $i++) {
                                                if(json_decode($userinformations[$i])!="" ){
                                                $picname=json_decode($userinformations[$i])->pic;
                                                $levelname = json_decode($userinformations[$i])->level;
                                                if(in_array($picname, ["MTPR"])&&in_array($levelname, ["selesai"])){
                                                $MTPRvalidation = "Aktif";
                                                break;
                                                }
                                                }
                                                }
                                                for ($i = 0; $i < count($userinformations); $i++) {
                                                    if(json_decode($userinformations[$i])!="" ){
                                                    $picname=json_decode($userinformations[$i])->pic;
                                                    $levelname = json_decode($userinformations[$i])->level;
                                                    if(in_array($picname, ["Manager Product
                                                    Engineering"])&&in_array($levelname, ["Senior Manager
                                                    Engineering"])){
                                                    $MPEvalidation = "Aktif";
                                                    break;
                                                    }
                                                    }
                                                    }
                                                    $totalSteps = 5; // Jumlah total langkah yang harus diselesaikan
                                                    $completedSteps = 0; // Inisialisasi jumlah langkah yang sudah
                                                    selesai
                                                    if ($MTPRsend == 'Aktif') {
                                                    $completedSteps++;
                                                    // if($PEshare == 'Nonaktif'){
                                                    // $nama_divisi="Product Engineering";
                                                    // $tugasdivisi = TugasDivisi::whereRaw('CAST(iddocument AS CHAR) =
                                                    ?', [$document->id])
                                                    // ->where('nama_divisi', $nama_divisi)
                                                    // ->first();
                                                    // if ($tugasdivisi['sudahdibaca']="sudah dibaca") {
                                                    // if($PEshare=="Nonaktif"){
                                                    // $PEshare = "Ongoing";
                                                    // }
                                                    // }
                                                    // else{
                                                    // $PEshare=="Belum Dibaca";
                                                    // }

                                                    // }
                                                    }
                                                    if ($PEshare == 'Aktif') {
                                                    $completedSteps++;
                                                    }
                                                    if(isset($projectpics)){for ($u = 0; $u < count($projectpics); $u++)
                                                        { $totalSteps++; if
                                                        ($unitpicvalidation[$projectpics[$u]]=='Aktif' ) {
                                                        $completedSteps++; } }} if ($PEmanagervalidation=='Aktif' ) {
                                                        $completedSteps++; if($seniormanagervalidation=="Nonaktif" ){
                                                        $seniormanagervalidation="Ongoing" ; } } if
                                                        ($seniormanagervalidation=='Aktif' ) { $completedSteps++;
                                                        if($MTPRvalidation=="Nonaktif" ){ $MTPRvalidation="Ongoing" ; }
                                                        } if ($MTPRvalidation=='Aktif' ) { $completedSteps++; }
                                                        $positionPercentage=intval(($completedSteps / $totalSteps) *
                                                        100); // Mengonversi ke integer $listdata=[];
                                                        $userinformations=json_decode($document->userinformations,
                                                        true);
                                                        $count = count($userinformations);

                                                        for ($su = 0; $su < $count; $su++) { $datadikirim=[];
                                                            $ringkasan=(json_decode($document->
                                                            userinformations,true)[$su]);
                                                            $datalokal=[];
                                                            $sumberdata=json_decode(json_decode($ringkasan)->userinformations);
                                                            $datadikirim['pic']=json_decode($ringkasan)->pic;
                                                            $datadikirim['level']=json_decode($ringkasan)->level;
                                                            $datalokal['nama penulis'] = isset($sumberdata->{'nama
                                                            penulis'}) ? $sumberdata->{'nama penulis'} :
                                                            $sumberdata->{'nama'};
                                                            $datalokal['email'] = isset($sumberdata->{'email'}) ?
                                                            $sumberdata->{'email'} : null;
                                                            $datalokal['conditionoffile'] =
                                                            isset($sumberdata->{'conditionoffile'}) ?
                                                            $sumberdata->{'conditionoffile'} : null;
                                                            $datalokal['conditionoffile2'] =
                                                            isset($sumberdata->{'conditionoffile2'}) ?
                                                            $sumberdata->{'conditionoffile2'} : null;
                                                            $datalokal['hasilreview'] =
                                                            isset($sumberdata->{'hasilreview'}) ?
                                                            $sumberdata->{'hasilreview'} : null;
                                                            $datalokal['sudahdibaca'] =
                                                            isset($sumberdata->{'sudahdibaca'}) ?
                                                            $sumberdata->{'sudahdibaca'} : null;
                                                            $datalokal['listfilenames']=isset(json_decode($ringkasan)->listfilenames)
                                                            ? json_decode($ringkasan)->listfilenames : [];
                                                            $datalokal['listmetadatas']=isset(json_decode($ringkasan)->listmetadatas)
                                                            ? json_decode($ringkasan)->listmetadatas : [];
                                                            $datalokal['listlinkfiles']=isset(json_decode($ringkasan)->listlinkfiles)
                                                            ? json_decode($ringkasan)->listlinkfiles : [];
                                                            $datadikirim['userinformations']=$datalokal;
                                                            $listdata[]=$datadikirim;
                                                            }
                                                            $datadikirimencoded=json_encode($listdata);

                                                            return
                                                            [$informasidokumenencoded,$datadikirimencoded,$positionPercentage,$unitpicvalidation,$projectpics,$PEsignature,
                                                            $userinformations,$selfunitvalidation,$PEmanagervalidation,$unitvalidation,$status,$indonesiatimestamps,$level,$MTPRsend,$PEshare,$seniormanagervalidation,$MTPRvalidation,$MPEvalidation,$arrayprojectpicscount];
                                                            }