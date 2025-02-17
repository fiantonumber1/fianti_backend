<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorDatabase extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_name', 'specialty'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}