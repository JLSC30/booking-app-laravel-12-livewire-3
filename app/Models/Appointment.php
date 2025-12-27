<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'service_id', 'date', 'start_time', 'end_time',
        'customer_name', 'customer_email', 'customer_phone', 'notes',
        'status', 'token', 'booking_code',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeActiveBookings($query, $branchId, $date)
    {
        return $query->where('branch_id', $branchId)
            ->where('date', $date)
            ->whereIn('status', 'confirmed');
    }

    protected $casts = [
        'date' => 'date',           // This converts the string to Carbon\Carbon
        'start_time' => 'string',   // optional, already string
        'end_time' => 'string',
    ];

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->start_time.' - '.$this->end_time;
    }
}
