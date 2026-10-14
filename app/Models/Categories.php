<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Categories extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'branch_id',
    ];
    protected array $encryptedAttributes = ['name', 'description'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
