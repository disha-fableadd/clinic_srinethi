<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class CodeMaster extends Model
{
    use HasFactory, EncryptsModelData;
    protected $table = 'code_master';
    protected $fillable = [
        'code',
        'branch_id'
    ];
    protected array $encryptedAttributes = ['code'];
}
