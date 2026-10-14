<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedTherapy extends Model
{
    use HasFactory;

    protected $table = 'assigned_therapies';

    protected $fillable = [
        'user_id',
        'patient_id',
        'therapy_id',
        'doctor_id',
        'type',
        'start_date',
        'end_date',
        'status',
        'branch_id',
    ];

    // Relationships (optional)
    public function therapy()
    {
        return $this->belongsTo(Therapy::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
