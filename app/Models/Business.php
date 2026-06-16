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
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


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
    'plan_id',
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

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function availabilityBreaks()
    {
        return $this->hasMany(BusinessAvailabilityBreak::class);
    }

    public function branding() : HasOne
    {
        return $this->hasOne(BusinessBrandingSettings::class);
    }

    public function plan() : BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isPremium(): bool
    {
        return $this->plan?->slug === 'premium';
    }

    public function canUseApprovalWorkflow(): bool
    {
        return $this->isPremium();
    }

    public function canUseWhatsapp(): bool
    {
        return $this->isPremium();
    }

    public function canUseReminders(): bool
    {
        return $this->isPremium();
    }
}
