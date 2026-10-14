<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class TreatmentBooking extends Model
{
    use HasFactory, EncryptsModelData;
    protected $table = 'treatment_booking';
    protected $fillable = [
        'patient_id',
        'branch_id',
        'treatment_id',
        'machine_id',
        'plan',
        'remain_amount',
        'status',
        'payment_date',
    ];

    protected $casts = [
        'machine_id' => 'array',
    ];
    // protected array $encryptedAttributes = ['plan', 'status'];
    public function getMachineDetailsAttribute()
    {
        // Decode JSON string from DB to array
        $machineIds = json_decode($this->machine_id, true);

        if (is_array($machineIds) && count($machineIds) > 0) {
            return \App\Models\Machine::whereIn('id', $machineIds)->get();
        }

        return collect([]);
    }
    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatment_id');
    }
    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function paymentHistory()
    {
        return $this->hasMany(TreatmentPaymentHistory::class, 'treatment_booking_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
