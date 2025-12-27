<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'day_of_week', 'start_time', 'end_time',
        'slot_interval_minutes', 'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',  // Casts to true/false automatically
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
