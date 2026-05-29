<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Business;
use App\Models\Service;
use App\Models\BusinessAvailability;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Appointment extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'business_id',
        'service_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'confirmed_at',
        'cancelled_at',
        'notes',
    ];
    
    protected $casts = [
        'appointment_date' => 'date',
        'confirmed_at' => 'datetime',
        'cancelled_date' => 'datetime',
    ];
    
    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function service() : BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    
}
