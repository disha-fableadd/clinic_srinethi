<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'status', 'content', 'branch_id',];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
      public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); 
    }
}
