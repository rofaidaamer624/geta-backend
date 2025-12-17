<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeTranslationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'source_language',
        'target_language',
        'notes',
        'file_path',
        'status',
    ];
}
