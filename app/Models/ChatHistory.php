<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $table = 'chat_history';
    protected $fillable = [
        'user_id',
        'session_id',
        'page_id',
        'user_question',
        'bot_response',
    ];

    //Get the user who owns this chat.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope to get chats for a specific session.

    public function scopeSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
    // Scope to get chats for a specific user.

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    //Helper to get the latest session for a user.

    public static function getLatestSession($userId)
    {
        return self::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->pluck('session_id')
            ->first();
    }
    public static function hasChatsInSession($userId, $sessionId): bool
    {
        return self::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->exists();
    }
}
