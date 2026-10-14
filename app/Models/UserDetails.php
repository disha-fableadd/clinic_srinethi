<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class UserDetails extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'user_details';

    protected $fillable = [
        'user_id',
        'address',
        'state',
        'city',
        'gender',
        'birth_date',
        'education',
        'experience',
        'shift',
        'salary',
    ];

    protected array $encryptedAttributes = [
        'address',
        'state',
        'city',
        'education',
        'experience',
    ];

    public function setSalaryAttribute($value): void
    {
        $this->attributes['salary'] = ($value === null || $value === '') ? 0 : $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
