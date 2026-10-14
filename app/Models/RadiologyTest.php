<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadiologyTest extends Model
{
    use HasFactory;

    protected $table = 'radiology_tests';

    protected $fillable = [
        'test_name',
        'test_code',
        'body_part',
        'cost',
        'gst_option',
        'product_gst',
        'report_format',
        'branch_id',
    ];

    protected $casts = [
        'product_gst' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
