<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Concerns\EncryptsModelData;

class Patients extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'patients';

    protected $fillable = [
        'user_id',
        'branch_id',
        'login_patient_id',
        'patient_unique_id',
        'fullname',
        'email',
        'password',
        'phone',
        'address',
        'age',
        'birthdate',
        'patient_type',
        'referral_source',
        'source_details',
        'state',
        'city',
        'profile',
        // 'blood_group',
        'note',
        // 'medical_history',
        'status',
        'diagnosis_id',
        'note_type',
        'note_interval',
        'symptoms',
        'improvement_history',
        'symptom_images',
        'symptom_remarks'

    ];

    protected $casts = [
        'note' => 'array',
        'symptoms' => 'array',
        'diagnosis_id' => 'array',
        'improvement_history' => 'array',
        'symptom_images' => 'array',
        'source_details' => 'array',

    ];
    protected array $encryptedAttributes = [
        'fullname',
        // 'email',
        'phone',
        'address',

        'referral_source',
        'state',
        'city',
        'symptom_remarks',
    ];
    public function getProfileAttribute($value)
    {

        $path = $this->getRawOriginal('profile');

        $basePath = env('IMAGE_PATH', '');

        return $path ? asset($basePath . $path) : asset($basePath . '/admin/assets/img/img1.png');
    }



    public function diagnosis()
    {
        return Diagnosis::whereIn('id', $this->diagnosis_id ?? [])->get();
    }

    // In Patients model
    public function getDiagnosisAttribute()
    {
        $ids = is_array($this->diagnosis_id) ? $this->diagnosis_id : json_decode($this->diagnosis_id, true) ?? [];
        return Diagnosis::whereIn('id', $ids)->get();
    }
    public function getDiagnosesNamesAttribute()
    {
        $diagnoses = $this->diagnoses; // uses the above accessor
        if ($diagnoses && $diagnoses->isNotEmpty()) {
            return $diagnoses->pluck('name')->implode(', ');
        }
        return 'No diagnosis';
    }








    public function treatmentBookings()
    {
        return $this->hasMany(TreatmentBooking::class, 'patient_id');
    }

    public function getNoteAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return array_map(function ($item) {
                if ($item['type'] === 'audio' && !empty($item['content'])) {
                    $item['content'] = asset(env('IMAGE_PATH') . $item['content']);
                }
                return $item;
            }, $value);
        }

        $notes = json_decode($value, true) ?? [];

        return array_map(function ($item) {
            if ($item['type'] === 'audio' && !empty($item['content'])) {
                $item['content'] = asset(env('IMAGE_PATH') . $item['content']);
            }
            return $item;
        }, $notes);
    }


    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }




    public function appointments()
    {
        return $this->hasMany(Appointments::class, 'patient_id');
    }


    public function followups()
    {
        return $this->hasMany(Followup::class, 'patient_id');
    }

    public function medicalReports()
    {
        return $this->hasMany(MedicalReport::class, 'patient_id');
    }

    public function dischargeDetails()
    {
        return $this->hasMany(PatientDischargeDets::class, 'patient_id');
    }


    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'patient_medicine')->withPivot('note')->withTimestamps();
    }

    public function getSymptomDetailsAttribute()
    {
        if (is_array($this->symptoms)) {
            return Symptom::whereIn('id', $this->symptoms)->get();
        }

        return [];
    }
    public function dailyData()
    {
        return $this->hasMany(DailyData::class, 'patient_id');
    }

    public function payments()
    {
        return $this->hasManyThrough(
            PaymentHistory::class,
            DailyData::class,
            'patient_id',   // Foreign key on daily_data table
            'daily_id',     // Foreign key on payment_history table
            'id',           // Local key on patients table
            'id'            // Local key on daily_data table
        );
    }

    public function therapies()
    {
        return $this->hasMany(AssignedTherapy::class, 'patient_id');
    }

    public function soaps()
    {
        return $this->hasMany(Soap::class, 'patient_id');
    }


    public function booking()
    {
        return $this->hasMany(TreatmentBooking::class, 'patient_id');
    }


    public function treatmentPayments()
    {
        return $this->hasManyThrough(
            TreatmentPaymentHistory::class, // final model
            TreatmentBooking::class,        // intermediate model
            'patient_id',                   // FK on treatment_booking table
            'treatment_booking_id',         // FK on treatment_payment_history table
            'id',                           // local key on patients
            'id'                            // local key on treatment_booking
        );
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
