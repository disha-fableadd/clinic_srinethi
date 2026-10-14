<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Soap extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'soap';

    protected $fillable = [
        'user_id',
        'branch_id',
        'patient_id',
        'date',
        'subjective',
        'objective',
        'assessment',
        'plan',

    ];
    protected array $encryptedArrayAttributes = ['subjective', 'objective', 'assessment', 'plan'];
    // Cast JSON fields to array for easy access
    protected $casts = [
        'subjective' => 'array',
        'objective' => 'array',
        'assessment' => 'array',
        'plan' => 'array',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
