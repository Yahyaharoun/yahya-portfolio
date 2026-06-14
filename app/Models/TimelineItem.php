<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'organization',
        'date_start',
        'date_end',
        'description',
        'location',
        'icon',
        'image_path',
        'is_current',
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
        'is_current' => 'boolean',
    ];
}
