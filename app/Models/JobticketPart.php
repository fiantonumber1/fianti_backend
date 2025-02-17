<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobticketPart extends Model
{
    use HasFactory;

    protected $table = 'jobticket_part';

    protected $fillable = [
        'unit_id',
        'proyek_type_id',
    ];

    

    public function jobticketidentitys()
    {
        return $this->hasMany(JobticketIdentity::class, 'jobticket_part_id');
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public static function indexjobticket($unitsingkatan,$listproject)
    {
        $alljobticket =  JobticketPart::with(['projectType', 'unit'])->get();
                            
        $revisiall = [];
        for ($i = 0; $i < count($listproject); $i++) {
            $key = str_replace(' ', '_', $listproject[$i]->title);
            $revisiall[$key]['alljobticket'] = collect($alljobticket)->where('proyek_type_id', $listproject[$i]->id)->all();
        }
        return [$alljobticket,$revisiall];
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
}
