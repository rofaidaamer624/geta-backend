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
    // 'short_description_ar',
    // 'short_description_en',
    'description_ar',
    'description_en',
    'price_text',
    'sort_order',
    'category',
    'icon_path',
];


protected $appends = ['icon_url'];

public function getIconUrlAttribute()
{
    return $this->icon_path ? asset('storage/' . $this->icon_path) : null;
}



    protected static function booted()
    {
        static::creating(function ($service) {
            if (empty($service->slug) && ! empty($service->name_en)) {
                $service->slug = Str::slug($service->name_en) . '-' . Str::random(5);
            }
        });
    }
}
