<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'pic', 'agenda_desc', 'agenda_unit', 'start', 'end', 'room', 'backgroundColor', 'borderColor', 'allDay', 'parent_id'
    ];

    // Fungsi khusus untuk mengubah array menjadi string
    public function parent()
    {
        return $this->belongsTo(Event::class, 'parent_id');
    }

    // Relasi children
    public function children()
    {
        return $this->hasMany(Event::class, 'parent_id');
    }
    public function convertListToString()
    {
        // Mengambil nilai agenda_unit dari model
        $list = json_decode($this->agenda_unit, true);

        // Menggabungkan elemen array menjadi string yang dipisahkan dengan koma
        if (is_array($list)) {
            return implode(',', $list);
        }

        return '';
    }

}
