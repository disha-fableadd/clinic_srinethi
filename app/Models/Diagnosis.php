<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Diagnosis extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'diagnosis';

    public $timestamps = false;
    protected $fillable = [
        'name',
        'branch_id',
        'description',
    ];
    protected array $encryptedAttributes = ['name', 'description'];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
