<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescriptions';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'medicine_id',
        'dosage',
        'frequency',
        'duration',
        'instruction',
    ];

    // Relationship with Patient
    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }

    // Relationship with Doctor
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // Relationship with Medicine
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
