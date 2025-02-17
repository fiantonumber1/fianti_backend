<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobticketStartedRev extends Model
{
    use HasFactory;

    protected $table = 'jobticket_started_rev';

    protected $fillable = [
        'revisionname',
        'jobticket_started_id',
        'start_time_run',
        'end_time_run',
        'total_elapsed_seconds',
        'revision_status',
        'checker_status',
        'approver_status',
        'checker_reason',
        'approver_reason',
        'drafter_id',
        'checker_id',
        'approver_id',
    ];

    public function jobticketstarted()
    {
        return $this->belongsTo(JobticketStarted::class, 'jobticket_started_id');
    }



    public function users()
    {
        return $this->hasMany(User::class, 'id', 'drafter_id')
            ->orWhere('id', 'checker_id')
            ->orWhere('id', 'approver_id');
    }

    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }

    public function tracking($status)
    {
        $files = $this->files;
        if ($status == 'closed' || $this->approver_status != null) {
            $this->drafter_status = "Approve";

            if ($this->checker_status == "Reject") {
                $this->checker_status = "Revision";
                $this->approver_status = "Revision";
            } else {
                $this->checker_status = "Approve";
                if ($this->checker_status == "Approve") {
                    if ($this->approver_status = "Reject") {
                        $this->approver_status = "Reject";
                    } else {
                        $this->approver_status = "Approve";
                    }
                }
            }



            if ($status == 'closed') {
                $this->approver_status = "Approve";
            }

        } elseif ($this->checker_status != null) {

            $this->drafter_status = "Approve";
            if ($this->checker_status == "Reject") {
                $this->checker_status = "Revision";
                $this->approver_status = "Revision";
            } else {
                $this->checker_status = "Approve";
                $this->approver_status = "Ongoing";
            }

        } elseif (count($files) != 0) {
            $this->drafter_status = "Approve";
            $this->checker_status = "Ongoing";
            $this->approver_status = "Nonaktif";
        } else {
            $this->drafter_status = "Ongoing";
            $this->checker_status = "Nonaktif";
            $this->approver_status = "Nonaktif";
        }

        return $this;
    }

    public function reasons()
    {
        return $this->hasMany(JobticketStartedRevReason::class, 'jobticket_started_rev_id');
    }



}
