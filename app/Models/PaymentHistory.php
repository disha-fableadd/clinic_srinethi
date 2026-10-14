<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

     protected $table = 'payment_history';

    protected $fillable = [
        'user_id',
        'daily_id',
        'amount',
        'payment_mode',
        'paid_type',
        'cash_amount',
        'online_amount',
        'remain_amount',
        'paid_amount'

    ];
public function dailyData()
{
    return $this->belongsTo(DailyData::class, 'daily_id');
}


}