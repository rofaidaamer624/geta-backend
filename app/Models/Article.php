<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

  protected $fillable = [
  'title_ar',
  'title_en',
  'slug',
  'summary_ar',
  'summary_en',
  'body_ar',
  'body_en',
  'image_url',
  'sort_order',
  'is_published',
  'published_at',
];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
}
