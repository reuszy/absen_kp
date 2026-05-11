<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'start_date', 'end_date', 'type', 'reason'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
