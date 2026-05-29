<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Business;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;


class Service extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'duration_minutes',
        'price',
        'color',
        'is_active',
        'confirmation_mode',
    ];


    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function appointments() : HasMany
    {
        return $this->hasMany(Appointment::class);
    }
    
}
