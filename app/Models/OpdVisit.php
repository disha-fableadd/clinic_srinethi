<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpdVisit extends Model
{
    use HasFactory;

    protected $table = 'opd_visits';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'visit_date',
        'chief_complaint',
        'diagnosis',
        'prescription',
        'consultation_fees',
        'status',
        'branch_id',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'prescription' => 'string',
        'consultation_fees' => 'float',
    ];
    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
     public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
