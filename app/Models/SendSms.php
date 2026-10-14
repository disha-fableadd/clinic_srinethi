<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendSms extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'template_id', 'service', 'sender_id','branch_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(SmsTemplate::class, 'template_id');
    }
       public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); 
    }
}
