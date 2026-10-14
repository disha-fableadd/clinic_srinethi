<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtProcedure extends Model
{
    use HasFactory;

    protected $table = 'ot_procedures';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'procedure_name',
        'procedure_date',
        'operation_notes',
        'status',
         'branch_id',
    ];

    /**
     * Relationships
     */

    // Each procedure belongs to one patient
    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }

    // Each procedure is performed by one doctor
    public function doctor()
    {
        return $this->belongsTo(User::class);
    }
           public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
       protected $casts = [
        'procedure_date' => 'date',  // or 'datetime' if you store time too
    ];

}
