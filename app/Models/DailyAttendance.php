<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAttendance extends Model
{   
    protected $guarded = [];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'machine_id', 'machine_id');
    }
}
