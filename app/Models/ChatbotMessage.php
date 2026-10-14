<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotMessage extends Model
{
    use HasFactory;
      protected $table = 'chatbot_messages';

         protected $fillable = [
        'user_message',
        'bot_response',
        'session_id',
    ];

}
