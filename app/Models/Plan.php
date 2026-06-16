<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'is_active',
    ];

    public function businesses() : HasMany
    {
        return $this->hasMany(Business::class);
    }

    public function isBasic(): bool
    {
        return $this->slug === 'basic';
    }

    public function isPremium(): bool
    {
        return $this->slug === 'premium';
    }

}
