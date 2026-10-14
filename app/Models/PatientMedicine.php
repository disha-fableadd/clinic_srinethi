<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class PatientMedicine extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'patient_medicine';

    protected $fillable = [
        'patient_id',
        'medicine_id',
        'treatment_id',
        'appointment_id',
        'note',
        'code',
        'value',
        'value_type',
        'branch_id'
    ];
    protected array $encryptedAttributes = ['code', 'value', 'value_type'];
    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
    public function appointment()
    {
        return $this->belongsTo(Appointments::class);
    }
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
