<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'users_id',
        'work_shift_id',
        'machine_id',
        'nip',
        'nama',
        'unit_kerja',
        'jabatan',
        'status',
        'faculty_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ]; 

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id', 'id');
    }

    public function facultyData()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Relasi ke akun User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
