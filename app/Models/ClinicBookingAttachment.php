<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicBookingAttachment extends Model
{
    use HasFactory;

    protected $table = 'clinic_booking_attachments';

    protected $fillable = [
        'booking_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $appends = ['file_url'];

    public function booking()
    {
        return $this->belongsTo(ClinicBooking::class, 'booking_id');
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? url($this->file_path) : null;
    }
}
