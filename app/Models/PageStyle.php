<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageStyle extends Model
{
    protected $fillable = [
        'artist_id',
        'image',
        'image_position',
        'image_custom_position_x',
        'image_custom_position_y',
        'image_extension',
        'device_type',
        'from_api',
        'env',
    ];

    protected $casts = [
        'from_api' => 'boolean',
    ];
}