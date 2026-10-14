<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathologyReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'test_id',
        'sample_collected_date',
        'report_date',
        'result',
        'report_file',
        'branch_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }

    public function test()
    {
        return $this->belongsTo(PathologyTest::class, 'test_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
