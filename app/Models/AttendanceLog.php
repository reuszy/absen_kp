<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'scan_time',
        'status_scan',
        'verify_mode',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'machine_id', 'machine_id');
    }
}
