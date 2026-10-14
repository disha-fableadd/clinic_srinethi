<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAssignDietChart extends Model
{
    use HasFactory;

    protected $table ='patient_assign_dietchart';

    protected $fillable =[
         'branch_id',
        'patient_id',
        'diet_template_id',
        'description',
    ];

     protected $casts = [
        'diet_template_id' => 'array',
        'description' => 'array',
    ];
     // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    // Access templates as a collection
    public function getTemplatesAttribute()
    {
        $ids = $this->diet_template_id ?? [];
        return Dietchart::whereIn('id', $ids)->get();
    }

    public function user()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }
     public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by'); // adjust User class if needed
    }
}
