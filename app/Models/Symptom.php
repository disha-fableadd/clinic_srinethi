<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Symptom extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'symptoms';
    protected $fillable = ['name', 'details', 'branch_id'];
    protected array $encryptedAttributes = ['name', 'details'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
