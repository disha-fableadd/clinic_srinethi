<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'patient_id',
        'description',
        'department',
        'service_id',
        'cost',
        'user_id',
        'branch_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patients::class, "patient_id", "id");
    }
    public function pathelogy_service()
    {
        return $this->belongsTo(PathologyTest::class, "service_id" ,"id");
    }
    public function radiology_service()
    {
        return $this->belongsTo(RadiologyTest::class, "service_id","id");
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
