<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Appointments extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'appointments';

    protected $fillable = [
        'user_id',
        'branch_id',
        'patient_id',
        'doctor_id',
        'treatment_id',
        'appoint_type',
        'status',
        'date',
        'duration',
        'clinic_location',

        'followup_update',

    ];
    protected array $encryptedAttributes = ['clinic_location', 'followup_update'];
    // Relationship with Patient
    public function patient()
    {
        return $this->belongsTo(Patients::class, "patient_id", "id");
    }

    // Relationship with Doctor


    // Relationship with Treatment
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    public function appointment_doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointment_history()
    {
        return $this->hasMany(AppointmentHistory::class, 'appointment_id', 'id');
    }
    public function followups()
    {
        return $this->hasMany(Followup::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
