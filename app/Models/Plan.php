<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration',
        'start_date',
        'end_date',
        'subtitle',
        'user_limit',
        'branch_limit',
        'storage_limit',
        'is_active',
        'sub_branch_id',
        'features',
        'total_amount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'user_limit' => 'integer',
        'branch_limit' => 'integer',
        'storage_limit' => 'integer',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'features' => 'array',
    ];

    /**
     * Calculate duration in months and days between start_date and end_date
     * @return float Duration in months (decimal format)
     */
    public function calculateDuration(): float
    {
        if ($this->start_date && $this->end_date) {
            $start = Carbon::parse($this->start_date);
            $end = Carbon::parse($this->end_date);
            
            // Calculate months and days
            $months = $end->diffInMonths($start);
            $daysRemaining = $end->copy()->subMonths($months)->diffInDays($start->copy()->addMonths($months));
            
            // Return months as integer (for total_amount calculation)
            // But we'll store the full duration string separately
            return $months + ($daysRemaining / 30);
        }
        return 0;
    }

    /**
     * Get duration formatted as string (e.g., "12 Months" or "11 Month 7 Day")
     * @return string Formatted duration
     */
    public function getDurationFormatted(): string
    {
        if ($this->start_date && $this->end_date) {
            $start = Carbon::parse($this->start_date);
            $end = Carbon::parse($this->end_date);
            
            $months = $end->diffInMonths($start);
            $daysRemaining = $end->copy()->subMonths($months)->diffInDays($start->copy()->addMonths($months));
            
            if ($months === 0 && $daysRemaining === 0) {
                return '0 Days';
            } elseif ($months === 0) {
                return $daysRemaining . ' Day' . ($daysRemaining > 1 ? 's' : '');
            } elseif ($daysRemaining === 0) {
                return $months . ' Month' . ($months > 1 ? 's' : '');
            } else {
                return $months . ' Month' . ($months > 1 ? 's' : '') . ' ' . $daysRemaining . ' Day' . ($daysRemaining > 1 ? 's' : '');
            }
        }
        return 'N/A';
    }

    /**
     * Calculate total amount based on price and duration (in months)
     * @return float Total amount
     */
    public function calculateTotalAmount(): float
    {
        if ($this->price && $this->start_date && $this->end_date) {
            $durationMonths = $this->calculateDuration();
            return $this->price * $durationMonths;
        }
        return 0;
    }

    /**
     * Boot the model to automatically calculate duration and total_amount before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            
        });
    }
}

