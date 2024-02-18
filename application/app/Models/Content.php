<?php

namespace App\Models;

use App\Enums\ContentState;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

class Content extends Model
{
    use HasUlids;

    protected $attributes = [
        'state' => ContentState::PENDING,
    ];

    protected $fillable = [
        'url',
    ];

    protected $casts = [
        'state' => ContentState::class,
        'meta' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function (Content $model) {
            $model->content = null;
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function getPathAttribute(): ?string
    {
        if (! $this->id) {
            return null;
        }

        return sprintf(
            '/contents/%s/%s.data',
            substr($this->id, 0, 10),
            $this->id,
        );
    }

    public function setContentAttribute(mixed $file): void
    {
        if (is_null($this->path)) {
            return;
        }

        if (Storage::exists($this->path)) {
            Storage::delete($this->path);
        }

        if (is_null($file)) {
            return;
        }

        if ($file instanceof StreamInterface) {
            $file->rewind();
        } elseif (is_resource($file)) {
            rewind($file);
        } elseif ($file instanceof SplFileInfo) {
            $file = fopen($file->getRealPath(), 'r');
        }

        Storage::put($this->path, $file);
    }

    public function getContentAttribute(): mixed
    {
        return Storage::readStream($this->path);
    }
}
