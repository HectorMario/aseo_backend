<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentAssignment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['appointment_id', 'assignee_id'];

    // Relación con el modelo 'Appointment'
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Relación con el modelo 'Assignee'
    public function assignee()
    {
        return $this->belongsTo(Assignee::class);
    }
}
