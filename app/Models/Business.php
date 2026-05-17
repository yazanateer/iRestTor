<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\BusinessAvailability;
use App\Models\BusinessDateOverride;
use App\Models\BusinessAvailabilityBreak;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;



class Business extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
    'name',
    'email',
    'slug',
    'phone',
    'password',
    'role',
    'business_id',
    ];

    public function users() : HasMany
    {
        return $this->hasMany(User::class);
    }

    public function services() : HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function availabilities() : HasMany
    {
        return $this->hasMany(BusinessAvailability::class);
    }

    public function appointments() : HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function dateOverrides()
    {
    return $this->hasMany(BusinessDateOverride::class);
    }

    public function availabilityBreaks()
    {
        return $this->hasMany(BusinessAvailabilityBreak::class);
    }
}
