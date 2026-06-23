<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicTimeSlot extends Model
{
    use HasFactory;

    protected $table = 'clinic_time_slots';

    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'max_bookings',
        'status',
    ];

    public function bookings()
    {
        return $this->hasMany(ClinicBooking::class, 'time_slot_id');
    }
}
