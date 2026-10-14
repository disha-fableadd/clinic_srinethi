<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;

class Medicine extends Model
{
    use HasFactory, EncryptsModelData;
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'unit',
        'status',
        'quantity',
        'manufacture_date',
        'medicine_unit',
        'expiry_date',
        'batch_no',
        'gst_option',
        'product_gst',
        'image',
        'user_id',
        'branch_id',
    ];

    protected $casts = [
        'product_gst' => 'array',
    ];
    protected array $encryptedAttributes = ['name', 'description', 'unit', 'batch_no'];
    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Categories::class);
    }


    // public function getimageAttribute($value)
    // {
    //     return $value ? asset(env('IMAGE_PATH') . "{$value}") : asset(env('IMAGE_PATH') . '/admin/assets/img/default.webp');
    // }

    public function getImageAttribute($value)
    {
        // If no value in DB → return default image
        if (!$value) {
            return asset(env('IMAGE_PATH') . '/admin/assets/img/default.webp');
        }

        // If value is already a full URL → return as is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // If IMAGE_PATH itself is a full URL → return value correctly
        if (filter_var(env('IMAGE_PATH'), FILTER_VALIDATE_URL)) {
            return rtrim(env('IMAGE_PATH'), '/') . '/' . $value;
        }

        // Else IMAGE_PATH is a folder → use asset()
        return asset(env('IMAGE_PATH') . $value);
    }



    public function patients()
    {
        return $this->belongsToMany(Patients::class, 'patient_medicine')->withPivot('note')->withTimestamps();
    }


    public function patientMedicines()
    {
        return $this->hasMany(PatientMedicine::class, 'medicine_id');
    }


    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
