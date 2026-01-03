<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'website_url',
        'sort_order',
        'is_active',
        'logo_path',
    ];

    /**
     * Append logo_url automatically
     */
    protected $appends = ['logo_url'];


  public function getLogoUrlAttribute()
{
    if (! $this->logo_path) {
        return null;
    }

    return url('/files/partners/' . $this->logo_path);
}

}
