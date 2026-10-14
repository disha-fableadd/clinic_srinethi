<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class PathologyTest extends Model
{
  use HasFactory, EncryptsModelData;

  protected $table = 'pathology_tests';
  protected $fillable = [
    'test_name',
    'test_code',
    'sample_type',
    'normal_range',
    'cost',
    'gst_option',
    'product_gst',
    'report_format',
    'branch_id'
  ];
  protected array $encryptedAttributes = ['test_name', 'test_code', 'sample_type', 'normal_range', 'report_format'];

  protected $casts = [
    'product_gst' => 'array',
  ];

  public function branch()
  {
    return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
  }
}
