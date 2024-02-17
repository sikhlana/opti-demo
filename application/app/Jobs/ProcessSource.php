<?php

namespace App\Jobs;

use App\Enums\SourceStatus;
use App\Jobs\Concerns\WorksWithSources;
use App\Models\Source;
use App\Services\UrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use RuntimeException;

class ProcessSource implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WorksWithSources;

    public int $tries = 10;

    public function __construct(
        protected Source $source,
        protected bool $force = false,
    ) {
    }

    public function handle(UrlService $service): void
    {
        if ($this->source->status === SourceStatus::COMPLETED && ! $this->force) {
            return;
        }

        $url = $service->normalize($this->source->url);
        $preflight = $this->http()->head($url);

        if (! is_null($preflight->transferStats)) {
            $url = strval($preflight->transferStats->getRequest()->getUri());
        }

        $hash = hash('sha384', $url);

        try {
            $this->source->fill([
                'status' => SourceStatus::PROCESSING,
                'canonical_url' => $url,
                'canonical_hash' => $hash,
                'attempts' => $this->source->attempts + 1,
            ]);

            if ($existing = Source::where('canonical_hash', $hash)->latest()->first()) {
                switch ($existing->status) {
                    case SourceStatus::PENDING:
                    case SourceStatus::PROCESSING:
                        $this->release(now()->addMinute());

                        return;

                    case SourceStatus::COMPLETED:
                        $this->source->fill([
                            'status' => SourceStatus::COMPLETED,
                            'content_type' => $existing->content_type,
                            'content_id' => $existing->content_id,
                        ]);

                        return;

                    case SourceStatus::FAILED:
                        break;
                }
            }
        } finally {
            $this->source->save();
        }

        $contentType = Str::of($preflight->header('content-type'))
            ->before(';')
            ->trim()
            ->value();

        $this->chain([
            match ($contentType) {
                'text/html' => new ScrapeWebPage($this->source),
                'application/pdf' => '',
                default => throw new RuntimeException('No parser found for the URL.'),
            },
        ]);
    }
}
