<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendEmail extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'template_id', 'sender_id','branch_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function emailtemplate()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }
      public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

   
}
