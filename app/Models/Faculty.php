<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $guarded = [];

    public function staff()
    {
        return $this->hasMany(Staff::class, 'faculty_id');
    }
}
