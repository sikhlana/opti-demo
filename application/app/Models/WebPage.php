<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebPage extends Model
{
    protected $fillable = [
        'html',
        'meta',
        'title',
        'body',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
