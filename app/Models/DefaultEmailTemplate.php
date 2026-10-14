<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultEmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'default_email_template';

    // Define the fillable fields
    protected $fillable = ['name', 'content'];
}
