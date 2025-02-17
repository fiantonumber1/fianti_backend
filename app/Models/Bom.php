<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    use HasFactory;
    

    protected $fillable = [
        'BOMnumber',
        'proyek_type',
        'revisi'
    ];

    public function bomoneshow($documents,$progressreports,$listdatadocumentencode){
        $groupprogress=[];
        foreach($progressreports as $progressreport){
            if (!empty($progressreport->revisi)) {
                $progressitemunit = json_decode($progressreport->revisi, true);
                foreach($progressitemunit as $index => $item){
                    $groupprogress[$item['nodokumen']]['spesifikasi_level'] = !empty($item['level']) ? $item['level'] : "Belum assign level";
                    $groupprogress[$item['nodokumen']]['spesifikasi_pic'] = !empty($item['drafter']) ? $item['drafter'] : "Belum assign pic";
                }
            }
        }
        
        
        $groupedKomats = []; // Variabel untuk menyimpan komats yang telah dikelompokkan berdasarkan kode material
        for ($i = 0; $i < count($documents); $i++) {
            $komats = json_decode(json_decode($documents[$i]->remaininformation)->komat);
            foreach ($komats as $komat) {
                $kodematerial = json_decode($komat)->kodematerial;
                // Periksa apakah kode material sudah ada dalam $groupedKomats
                if (!array_key_exists($kodematerial, $groupedKomats)) {
                    $groupedKomats[$kodematerial] = [];
                }
                $sumberinformasi= json_decode(json_decode($listdatadocumentencode,true)[$documents[$i]->id],true);
                $positionPercentage=$sumberinformasi['positionPercentage'];
                $PEcombineworkstatus=$sumberinformasi['PEcombineworkstatus']??"";
                // Tambahkan informasi komat ke dalam grup yang sesuai
                $komatitem=json_decode($komat);
                $groupedKomats[$kodematerial]['supplier'][] = $komatitem->supplier;
                $groupedKomats[$kodematerial]['komponen'] = $komatitem->komponen;
                $groupedKomats[$kodematerial]['memoname'][] = $documents[$i]->documentname;
                $groupedKomats[$kodematerial]['memoid'][] = $documents[$i]->id;
                $groupedKomats[$kodematerial]['memostatus'][] = $documents[$i]->documentstatus;
                $groupedKomats[$kodematerial]['percentage'][] = $positionPercentage;
                $groupedKomats[$kodematerial]['PEcombineworkstatus'][] = $PEcombineworkstatus;
            }
        }
        $revisiall = json_decode($this->revisi, true);
        $seniorpercentage = 0;
        $satuanitem = count($revisiall);
        $materialclosed=0;
        $materialopened=0;
        foreach ($revisiall as $index => $item) {
            if (isset($groupedKomats[$item['kodematerial']])) {
                $partialpercentage = 0;
                $listpercentage = $groupedKomats[$item['kodematerial']]['percentage'];
                $satuan = count($listpercentage);
                foreach ($listpercentage as $percentage) {
                    $partialpercentage += ($percentage / $satuan);
                }

                $seniorpercentage += $partialpercentage/$satuanitem;
                $groupedKomats[$item['kodematerial']]['totalpercentage']=$partialpercentage;
            }
        }
        $revisi=json_decode($this->revisi, true);
        foreach ($revisi as $index => $item){
            if(isset($groupedKomats[$item['kodematerial']])){
                $totalpercentage = $groupedKomats[$item['kodematerial']]['totalpercentage']; 
            }else{
                $totalpercentage = 0; 
            }
            if(($totalpercentage)==100){
                $materialclosed++;
            }else{
                $materialopened++;
            }
        }
        
        return [$groupedKomats,$groupprogress,$seniorpercentage,$materialopened,$materialclosed];
    }

    public function revisiData(){
        return json_decode($this->revisi, true);
    }

    public function komat($index){
        $revisiData = $this->revisiData();

        // Check if the index exists in the revisi data
        if (!isset($revisiData[$index])) {
            return response()->json(['error' => 'Invalid index specified'], 400);
        }

        // Extract the details for logging
        $kodematerial = $revisiData[$index]['kodematerial'] ?? "";
        $material = $revisiData[$index]['material'] ?? "";
        $status = $revisiData[$index]['status'] ?? "";
        $spesifikasi = $revisiData[$index]['spesifikasi'] ?? "";
        return [$revisiData,$kodematerial,$material,$status,$spesifikasi];
    }

    public static function percentageandcount($listproject, $revisiall)
    {

        $boms = Bom::orderBy('created_at', 'desc')->get();
        foreach ($listproject as $keyan) {
            //BOM persentase dan jumlah
            $countvalue = 0;
            $groupbomnumberpercentage = 0;
            if($keyan!="All"){
                $filteredBoms = collect($boms)->where('proyek_type', $keyan)->all();
            }else{
                $filteredBoms =$boms;
            }
            $materialopenedall=0;
            $materialclosedall=0;
            foreach ($filteredBoms as $bom) {
                $seniorpercentage=0;
                $materialopened=0;
                $materialclosed=0;
                $groupbomnumberpercentage += $seniorpercentage;
                $countvalue++;
                $materialopenedall+=$materialopened;
                $materialclosedall+=$materialclosed;
            }
        
            $groupbomnumberpercentagereal = $countvalue > 0 ? $groupbomnumberpercentage / $countvalue : 0;
            $presentasebypart=$groupbomnumberpercentagereal; //presentaseselesai
            if($materialclosedall!=0 &&$materialopenedall!=0){
                $presentaseterbuka=$materialclosedall/($materialclosedall+$materialopenedall)*100;
                $presentasetertutup = 100-$presentaseterbuka;
            }else{
                $presentaseterbuka=0;
                $presentasetertutup = 100-$presentaseterbuka;
            }
            
            $revisiall[$keyan]['persentasebom'] = [
                'terselesaikan' => $presentaseterbuka,
                'tidak terselesaikan' =>  $presentasetertutup,
            ];
            $revisiall[$keyan]['jumlahbom'] = [
                'terselesaikan' => $materialclosedall,
                'tidak terselesaikan' =>  $materialopenedall,
            ];
        }

        return $revisiall;
    }
}
