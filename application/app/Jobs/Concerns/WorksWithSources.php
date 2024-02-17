<?php

namespace App\Jobs\Concerns;

use App\Enums\SourceStatus;
use App\Models\Source;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Throwable;

trait WorksWithSources
{
    public int $uniqueFor = 3600;

    public function __construct(
        protected Source $source,
    ) {
    }

    public function uniqueId(): string
    {
        return $this->source->id;
    }

    public function failed(Throwable $e): void
    {
        $this->source->update([
            'status' => SourceStatus::FAILED,
            'error' => $e->getMessage(),
        ]);
    }

    protected function http(): PendingRequest
    {
        return Http::maxRedirects(3)
            ->withUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:122.0) Gecko/20100101 Firefox/122.0')
            ->throw();
    }
}
