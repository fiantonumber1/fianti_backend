<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newprogressreporthistory extends Model
{

    use HasFactory;

    // Define the table associated with the model
    protected $table = 'newprogressreporthistorys';

    // Specify the attributes that are mass assignable


    protected $fillable = [
        'newprogressreport_id',
        'nodokumen',
        'namadokumen',
        'level_id',
        'drafter',
        'checker',


        'documentkind_id',
        'realisasi',
        'rev',

        'status',
        'temporystatus',
        'dcr',
        'startreleasedate',
        'deadlinereleasedate',
        'realisasidate',
        'papersize',
        'sheet',
    ];


    // Optionally, specify if you want to use timestamps
    public $timestamps = true;

    // Define the relationship with the NewProgressReport model
    public function newProgressReport()
    {
        return $this->belongsTo(Newprogressreport::class, 'newprogressreport_id');
    }
    public function dailyNotifications()
    {
        return $this->belongsToMany(
            DailyNotification::class,
            'daily_notification_new_progress_report',
            'new_progress_report_history_id',
            'daily_notification_id'
        )->withTimestamps();
    }



    public function documentKind()
    {
        return $this->belongsTo(NewProgressReportDocumentKind::class, 'documentkind_id');
    }

    public function levelKind()
    {
        return $this->belongsTo(NewProgressReportsLevel::class, 'level_id');
    }

    public function jobticketHistories()
    {
        return $this->hasMany(JobticketHistory::class, 'newprogressreporthistory_id');
    }
}
