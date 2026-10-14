<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiCredential extends Model
{
    use HasFactory;
    
    protected $fillable = ['service_name', 'api_key', 'is_default'];
     public $timestamps = false;
}
