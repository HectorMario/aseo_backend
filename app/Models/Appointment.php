<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_date',
        'appointment_time',
        'client_id',
        'status',
    ];

    /**
     * Get the client that owns the appointment.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function appointmentAssignments()
    {
        return $this->hasMany(AppointmentAssignment::class);
    }

    
}
