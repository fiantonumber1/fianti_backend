<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LogController extends Controller
{
    public function updatelog($id,$pesan,$aksi,$user,$jenisdata)
    {
        $data=[
            'id'=>$id,
            'pesan'=>$pesan, 
        ];
        // Return view with the updated BOM
        $logs = Log::create([
            'message' => json_encode($data),
            'level' => 'INFO',
            'aksi'=>$aksi,
            'user'=>$user,
            'jenisdata'=>$jenisdata
        ]);
    }
}
