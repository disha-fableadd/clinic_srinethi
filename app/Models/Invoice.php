<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'type',
        'type_id',
        'date',
        'instruction',
        'types_details',
        'grand_total',
        'tax',
        'amount',
        'discount_type',
        'discount',
        'payment_type',
        'payment_status',
        'pdf_url',
          'branch_id',
    ];

    protected $casts = [
        'types_details' => 'array',
    ];


    public function patient()
{
    return $this->belongsTo(Patients::class, 'patient_id');
}
      public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }

}
