<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadiologyReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'test_id',
        'report_date',
        'report_file',
        'converted_image',
        'branch_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    public function test()
    {
        return $this->belongsTo(RadiologyTest::class, 'test_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
