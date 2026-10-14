<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Treatment extends Model
{
    use HasFactory, EncryptsModelData;

    protected $table = 'treatments';

    protected $fillable = [
        'doctor_id',
        'name',
        'price',
        'description',
        'gst_option',
        'product_gst',
        'user_id',
        'branch_id'
    ];
    // protected array $encryptedAttributes = ['name', 'description'];
    protected $casts = [
        'product_gst' => 'array',
    ];

    // Relationship with Doctor (assuming Doctor is a user with a specific role)
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patients()
    {
        return $this->belongsTo(Patients::class);
    }

    public function medicalReports()
    {
        return $this->morphMany(MedicalReport::class, 'type');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
