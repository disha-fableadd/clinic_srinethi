<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietChart extends Model
{
    use HasFactory;
    protected $table = 'diet_charts';

    protected $fillable = [
        'branch_id',
        'name',
        'title',
        'description', 
        'image',
        'time',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'image' => 'array',
        'time' => 'array',
    ];
}
