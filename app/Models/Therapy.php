<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Therapy extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'therapy';

    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'duration_minutes',
        'cost',
        'gst_option',
        'product_gst',
        'status',
    ];
    protected array $encryptedAttributes = ['name', 'description'];
    protected $casts = [
        'product_gst' => 'array',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
