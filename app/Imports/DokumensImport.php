<?php

namespace App\Imports;

use DateTime;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DokumensImport implements ToModel, WithStartRow,WithLimit
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function startRow(): int
    {
        return 2;
    }
    public function limit(): int
    {
        return 10000;
    }
    public function model(array $row)
    {
        $informasiupload = "";
        $userinformations = [];
        $listfilenames = [];
        $listmetadatas = [];
        $listlinkfiles = [];
        $userName = auth()->user()->name;
        $userEmail = auth()->user()->email;
     
        $userInfo = [
            'nama' => $userName,
            'email' => $userEmail,
            'sudahdibaca' => 'Sudah',
            'hasilreview' => 'Ya, dapat diterima',
            'author' => $userName,
            'comment' => '',
            'time' => now(),
            'conditionoffile' => "pembukadokumen",
        ];
        $userInfoJson = json_encode($userInfo);
        $Infounit = [
            'pic' => auth()->user()->rule,
            'level' => "pembukadokumen",
            'userinformations' => $userInfoJson,
            'listfilenames' => $listfilenames,
            'listmetadatas' => $listmetadatas,
            'listlinkfiles' => $listlinkfiles,
            'author' => auth()->user()->name,
            'time' => now(),
        ];
    
        $userinformations[] = json_encode($Infounit);
    
        
        if(isset($row[7])){
            $waktuManual =DateTime::createFromFormat('d/m/Y', $row[7])->format('Y-m-d');
            if($waktuManual !== false) {
                $timeline = [
                'documentopened' => $waktuManual,
                ];}
            else{
                $timeline = [
                    'documentopened' => now()->subYear(), // Mengurangi satu tahun dari tanggal saat ini
                ];
            }
        
        }
        else{$timeline = [
            'documentopened' => now(),
        ];}
        
        $komatlist=[];
        $looping=0;
        $komponenvalue=$row[4];
        $suppliervalue=$row[5];
        $kodematerialvalue=$row[6];
        if(isset($kodematerialvalue)){$looping=1;}
        $komattunggal=[
            'komponen' => $komponenvalue,
            'supplier' => $suppliervalue,
            'kodematerial' => $kodematerialvalue,
        ];
        for ($i = 0; $i < $looping; $i++) {
            $komatlist[] = json_encode($komattunggal);
        }
        $remaininformation = [
            'komat' => json_encode($komatlist),
        ];
        $timelineJson = json_encode($timeline);
        $remaininformationJson = json_encode($remaininformation);

        return new NewMemo([
            'documentname' => $row[0],
            'documentnumber' => $row[1],
            'project_type' => $row[2],
            'documentstatus' => $row[3],
            'memokind' => "",
            'memoorigin' => "",
            'userinformations' => json_encode($userinformations),
            'timeline' => $timelineJson,
            'remaininformation' => $remaininformationJson,
            'asliordummy'=> "asli",
            'category' => "memo",
        ]);
    }




    
}
