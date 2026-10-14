<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Machine extends Model
{
    use HasFactory, EncryptsModelData;

    protected $fillable = [
        'branch_id',
        'name',
        'price',
        'description',
    ];
    protected array $encryptedAttributes = ['name', 'description'];
    // If branch relation exists
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
