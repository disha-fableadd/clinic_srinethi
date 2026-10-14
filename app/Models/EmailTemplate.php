<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'email_template';

    protected $fillable = [
        'user_id',
        'template_id',
        'name',
        'status',
        'img1',
        'img2',
        'img3',
         'branch_id',
    ];


    public function defaultTemplate()
{
    return $this->belongsTo(DefaultEmailTemplate::class, 'template_id','id');
}
  public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id'); // correct relation
    }

}
