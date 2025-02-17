<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'user_id', 'patient_name', 'appointment_time', 'notes'];

    // Relationship to DoctorDatabase model
    public function doctor()
    {
        return $this->belongsTo(DoctorDatabase::class);
    }

    // Relationship to User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
