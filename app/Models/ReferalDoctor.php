<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferalDoctor extends Model
{
    use HasFactory;
    protected $table = 'referal_doctors';

    protected $fillable = [
        'doctor_name',
         'branch_id',
        'specialist',
        'email',
        'phone_number',
    ];
        public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
