<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class MedicineUnit extends Model
{
    use HasFactory, EncryptsModelData;
    protected $fillable = [
        'unit',
        'branch_id',
    ];
    protected array $encryptedAttributes = ['unit'];
}
