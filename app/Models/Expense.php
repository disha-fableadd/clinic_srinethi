<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Expense extends Model
{
    use HasFactory, EncryptsModelData;
    protected $table = 'expenses';
    protected $fillable = [
        'user_id',
        'branch_id',
        'date_time',
        'amount',
        'service',
        'comment',
    ];

    protected array $encryptedAttributes = ['service', 'comment'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // correct relation
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
