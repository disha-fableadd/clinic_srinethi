<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'info',
        'is_read',
    ];

    protected $casts = [
        'info' => 'array',
        'is_read' => 'boolean',
    ];

    // Store a single notification
     public static function store($message = "", $receiver_id = null, $module_id = null, $module = null, $sender_id = null, $patient_id = null)
    {
        return self::create([
            'sender_id' => $sender_id ?? auth()->id(),
            'receiver_id' => $receiver_id,
            'info' => [
                'message' => $message,
                'module' => $module,
                'module_id' => $module_id,
                'sender_id' => $sender_id ?? auth()->id(),
                'receiver_id' => $receiver_id,
                'type' => 'notification',
                'patient_id' => $patient_id, // ✅ Store patient_id
            ],
            'is_read' => false,
        ]);
    }

    // Store notifications for multiple receivers
    public static function notifyMultiple($message, array $receiverIds, $module_id = null, $module = null, $sender_id = null, $patient_id = null)
    {
        foreach ($receiverIds as $receiverId) {
            self::store($message, $receiverId, $module_id, $module, $sender_id, $patient_id);
        }
    }

    // Backward-compatible alias used by follow-up flows.
    public static function notifyUsers($message, array $receiverIds, $module_id = null, $module = null, $sender_id = null, $patient_id = null)
    {
        self::notifyMultiple($message, $receiverIds, $module_id, $module, $sender_id, $patient_id);
    }

    /**
     * Resolve user ids by role names, optionally scoped to a branch and excluding ids.
     */
    public static function userIdsForRoles(array $roleNames, $branchId = null, array $excludeUserIds = []): array
    {
        $query = User::query()
            ->whereHas('role', function ($roleQuery) use ($roleNames) {
                $roleQuery->whereIn('name', $roleNames);
            });

        if (!is_null($branchId)) {
            $query->where('branch_id', $branchId);
        }

        if (!empty($excludeUserIds)) {
            $query->whereNotIn('id', $excludeUserIds);
        }

        return $query->pluck('id')
            ->filter(fn ($id) => !is_null($id))
            ->unique()
            ->values()
            ->all();
    }
}
