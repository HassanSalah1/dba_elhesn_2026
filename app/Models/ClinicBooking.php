<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicBooking extends Model
{
    use HasFactory;

    protected $table = 'clinic_bookings';

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'time_slot_id',
        'booking_date',
        'is_for_other',
        'other_name',
        'other_phone',
        'other_country_code',
        'injury_type',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(ClinicTimeSlot::class, 'time_slot_id');
    }

    public function attachments()
    {
        return $this->hasMany(ClinicBookingAttachment::class, 'booking_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
}
