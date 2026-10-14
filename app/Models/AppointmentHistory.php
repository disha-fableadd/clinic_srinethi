<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentHistory extends Model
{
    use HasFactory;


    protected $table = 'appointment_history';

    protected $fillable = [
        'appointment_id',
          'branch_id',
        'date',
        'time',
        'status',
        'comment',
        'created_by',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointments::class,"appointment_id");
    }
       public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
