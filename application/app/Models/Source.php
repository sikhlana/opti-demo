<?php

namespace App\Models;

use App\Enums\SourceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Source extends Model
{
    use HasUuids;

    protected $attributes = [
        'status' => SourceStatus::PENDING,
        'attempts' => 0,
    ];

    protected $fillable = [
        'url',
        'canonical_url',
        'canonical_hash',
        'status',
        'attempts',
        'error',
    ];

    protected $hidden = [
        'canonical_hash',
    ];

    protected $casts = [
        'status' => SourceStatus::class,
    ];

    public function content(): MorphTo
    {
        return $this->morphTo();
    }
}
