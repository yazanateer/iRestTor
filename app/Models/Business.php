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
    'address',
    'timezone',
    'is_active',
    'plan_id',
    'booking_window_days',
    'trial_ends_at',
    'tos_accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'tos_accepted_at' => 'datetime',
        ];
    }

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

    /** Business has a paid plan assigned (present or future payment feature). */
    public function isPaid(): bool
    {
        return ! is_null($this->plan_id);
    }

    /** plan_id null AND still within the trial window. */
    public function onTrial(): bool
    {
        return is_null($this->plan_id)
            && ! is_null($this->trial_ends_at)
            && now()->lessThanOrEqualTo($this->trial_ends_at);
    }

    /** plan_id null AND trial window has passed. */
    public function trialExpired(): bool
    {
        return is_null($this->plan_id)
            && ! is_null($this->trial_ends_at)
            && now()->greaterThan($this->trial_ends_at);
    }

    /** Remaining whole days of trial, floored at 0; null when not on a trial. */
    public function trialDaysRemaining(): ?int
    {
        if (is_null($this->trial_ends_at) || ! is_null($this->plan_id)) {
            return null;
        }

        return max(0, (int) ceil(now()->diffInDays($this->trial_ends_at, false)));
    }
}
