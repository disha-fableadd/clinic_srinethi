<?php

namespace App\Models;

use App\Models\Concerns\EncryptsModelData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    use HasFactory, EncryptsModelData;


    protected $fillable = [
        'user_id',
        'patient_id',
        'treatment_id',
        'doctor_id',
        'date',
        'followup_type',
        'followup_update',
        'branch_id',
    ];

    protected array $encryptedAttributes = ['followup_type', 'followup_update'];

    // Relationships
    // public function patient()
    // {
    //     return $this->belongsTo(Patients::class);
    // }

    // public function treatment()
    // {
    //     return $this->belongsTo(Treatment::class);
    // }

    // public function doctor()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function followup_doctor()
    // {
    //     return $this->belongsTo(User::class, 'doctor_id'); // specify 'doctor_id' if it's not default 'user_id'
    // }

    // public function branch()
    // {
    //     return $this->belongsTo(Branch::class, 'branch_id');
    // }

    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatment_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
     public function followup_doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id'); // specify 'doctor_id' if it's not default 'user_id'
    }
}
