<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class BookingVerification extends Model
{
    use HasFactory, Notifiable;
    
    protected $fillable = [
        'business_id',
        'service_id',
        'appointment_date',
        'start_time',
        'end_time',
        'customer_name',
        'customer_phone',
        'customer_email',
        'code_hash',
        'expires_at',
        'verified_at',
        'attempts',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
