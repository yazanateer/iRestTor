<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    protected $fillable = [
        'full_name',
        'business_name',
        'phone',
        'business_type',
        'message',
        'locale',
        'status',
    ];
}
