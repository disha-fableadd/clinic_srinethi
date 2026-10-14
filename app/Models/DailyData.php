<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class DailyData extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'daily_data';

    protected $fillable = [
        'user_id',
        'patient_id',
        'treatment_id',
        'date',
        'remain_amount',
        'status',
        'collect_by_id',
        'comment',
        'branch_id',
    ];

    protected array $encryptedAttributes = ['comment',];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collect_by_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patients::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }


    public function paymentHistories()
    {
        return $this->hasMany(PaymentHistory::class, 'daily_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
