<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyNotification extends Model
{
    use HasFactory;

    protected $table = 'daily_notifications';
    protected $fillable = ['user_id', 'name', 'day', 'read_status', 'notif_harian_unit_id'];

    /**
     * Relasi many-to-many dengan NewProgressReportHistory.
     */
    public function newProgressReportHistories()
    {
        return $this->belongsToMany(
            Newprogressreporthistory::class,
            'daily_notification_new_progress_report', // Nama tabel pivot
            'daily_notification_id', // Foreign key di tabel pivot
            'new_progress_report_history_id' // Foreign key di tabel pivot
        )->withTimestamps(); // Jika ingin menyimpan timestamps di tabel pivot
    }
    public function notifHarianUnit()
    {
        return $this->belongsTo(NotifHarianUnit::class, 'notif_harian_unit_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'daily_notification_user')->withTimestamps();
    }

}
