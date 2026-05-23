<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class BusinessBrandingSettings extends Model
{
    protected $fillable = [
        'business_id',
        'logo_path',
        'cover_image_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'public_title',
        'public_subtitle',
        'public_description',
        'theme_style',
    ];

    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
