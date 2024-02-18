<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use SplFileInfo;

class Image extends Model
{
    use HasUlids;

    protected $fillable = [
        'source',
        'content_type',
    ];

    protected $hidden = [
        'content_id',
        'source',
    ];

    protected $appends = [
        'link',
    ];

    protected static function booted()
    {
        static::deleting(function (Image $model) {
            $model->data = null;
        });
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
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

    public function setDataAttribute(mixed $file): void
    {
        if (is_null($this->path)) {
            return;
        }

        try {
            if (Storage::exists($this->path)) {
                Storage::delete($this->path);
            }

            $this->hash = null;
            $this->size = null;

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

            $this->hash = Storage::checksum($this->path, ['checksum_algo' => 'sha256']) ?: throw new RuntimeException('Unable to calculate checksum.');
            $this->size = Storage::size($this->path) ?: throw new RuntimeException('Unable to calculate file size.');
        } finally {
            $this->save();
        }
    }

    public function getDataAttribute(): mixed
    {
        return Storage::readStream($this->path);
    }

    public function getLinkAttribute(): string
    {
        return url()->route('images.show', $this);
    }
}
