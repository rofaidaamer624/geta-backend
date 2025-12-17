<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'short_description',
        'description',
        'price_text',
        'sort_order',
        'icon_path',
        // 'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($service) {
            if (empty($service->slug) && ! empty($service->name_en)) {
                $service->slug = Str::slug($service->name_en) . '-' . Str::random(5);
            }
        });
    }
}
