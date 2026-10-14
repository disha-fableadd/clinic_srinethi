<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentPaymentHistory extends Model
{
    use HasFactory;
    protected $table = 'treatment_payment_history';
    protected $fillable = [
        'id',
        'user_id',
        'treatment_booking_id',
        'amount',
        'payment_mode',
        'paid_type',
        'cash',
        'online',
        'paid_amount',
        'remain_amount',
         'branch_id'

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // if you track who recorded the payment
    }

    public function booking()
    {
        return $this->belongsTo(TreatmentBooking::class, 'treatment_booking_id');
    }
       public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
