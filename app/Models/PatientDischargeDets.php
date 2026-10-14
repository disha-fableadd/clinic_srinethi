<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDischargeDets extends Model
{
    use HasFactory;

    protected $table = 'patient_discharge_dets';

    protected $fillable = [
        'patient_id',
        'ipd_id',
        'discharge_date',
        'total_bill',
        'amount_paid',
        'payment_status',
        'gst_option',
        'product_gst',
        'discharge_note',
        'user_id',
        'branch_id'
    ];

    protected $casts = [
        'product_gst' => 'array',
    ];

    // Relationship with Patient
    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }

    // Relationship with Doctor
    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Treatment
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
    public function ipd()
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
