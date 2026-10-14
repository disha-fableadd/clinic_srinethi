<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAssignAssessment extends Model
{
    use HasFactory;
    protected $table = 'patient_assign_assessment';

    protected $fillable = [
        'branch_id',
        'patient_id',
        'template_id',
        'description',
    ];

    protected $casts = [
        'template_id' => 'array',
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

    // public function template()
    // {
    //     return $this->belongsTo(AssessmentTemplate::class, 'template_id');
    // } 
    
    public function getTemplatesAttribute()
    {
        $ids = $this->template_id ?? [];
        return AssessmentTemplate::whereIn('id', $ids)->get();
    }


    public function user()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

  
}
