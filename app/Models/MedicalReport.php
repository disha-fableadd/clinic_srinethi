<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MedicalReport extends Model
{
    use HasFactory;

    protected $table = 'medical_reports';

    protected $fillable = [
        'patient_id',
        'date',
        'report_type',
        'description',
        'file_path',
        'user_id',
        'branch_id',

    ];


    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }


    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    public function getFilePathAttribute($value)
    {
        return $value ? asset(env('IMAGE_PATH') . "{$value}") : asset(env('IMAGE_PATH') . 'admin/assets/img/repot.jpg');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
