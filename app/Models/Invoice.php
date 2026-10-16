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
        'branch_id',
        'gst_enabled',
    ];

    protected $attributes = [
        'discount_type' => 'fixed_amount',
        'discount' => 0,
    ];

    protected $casts = [
        'types_details' => 'array',
        'gst_enabled' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->discount_type)) {
                $invoice->discount_type = 'fixed_amount';
            }
            if ($invoice->discount === null || $invoice->discount === '') {
                $invoice->discount = 0;
            }
        });
    }


    public function patient()
{
    return $this->belongsTo(Patients::class, 'patient_id');
}
      public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }

}
