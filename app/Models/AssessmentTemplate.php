<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\EncryptsModelData;


class AssessmentTemplate extends Model
{
    use HasFactory, EncryptsModelData;
    protected $table = 'assessment_template';

    protected $fillable = [
        'name',
        'title',
        'description',
        'image',
        'branch_id',
    ];
    protected array $encryptedAttributes = ['name'];

    protected array $encryptedArrayAttributes = ['title', 'description', 'image'];
    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'image' => 'array',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }
}
