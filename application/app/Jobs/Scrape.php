<?php

namespace App\Jobs;

use App\Enums\ContentState;
use App\Exceptions\MismatchedCanonicalUrl;
use App\Models\Content;
use App\Scrapers\HtmlScraper;
use App\Scrapers\PdfScraper;
use App\Scrapers\Scraper;
use App\Services\UrlService;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class Scrape implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 3600;

    public int $tries = 3;

    public function __construct(
        protected Content $content,
        protected bool $force = false,
    ) {
    }

    public function uniqueId(): string
    {
        return "scrape:{$this->content->id}";
    }

    public function handle(UrlService $service): void
    {
        if (! $this->force && ($this->content->state === ContentState::COMPLETED)) {
            return;
        }

        $this->content->state = ContentState::PROCESSING;
        $url = $service->normalize($this->content->url);
        $canonicalized = false;

        restart:
        $response = $this->http()->get($url);

        if ($response->failed()) {
            if ($response->tooManyRequests()) {
                $this->release(now()->addSeconds(10));

                return;
            }

            throw $response->toException();
        }

        $contentType = Str::of($response->header('content-type'))
            ->before(';')
            ->trim()
            ->value();

        $this->content->content = $response->toPsrResponse()->getBody();
        $this->content->content_type = $contentType;

        try {
            if (! $canonicalized) {
                $old = $url;

                if (! is_null($effective = $response->effectiveUri())) {
                    $url = strval($effective);
                }

                if (! empty($link = trim($response->header('link')))) {
                    foreach (explode(',', $link) as $l) {
                        if (preg_match('/<(.*?)>;\s?rel=[\'"]?canonical[\'"]?/u', $l, $matches)) {
                            $url = trim($matches[1]);
                            break;
                        }
                    }
                }

                $canonicalized = true;

                if ($old !== $url) {
                    goto restart;
                }
            }

            $hash = hash('sha384', $url);

            if (! $this->force && ($existing = Content::where('hash', $hash)->latest()->first())) {
                switch ($existing->state) {
                    case ContentState::PENDING:
                    case ContentState::PROCESSING:
                        $this->release(now()->addMinute());

                        return;

                    case ContentState::COMPLETED:
                        $this->content->parent()->associate($existing);
                        $this->content->state = ContentState::COMPLETED;
                        $this->content->content = null;
                        $this->content->content_type = null;

                        return;
                }
            }

            $this->content->canonical_url = $url;
            $this->content->hash = $hash;
        } finally {
            $this->content->save();
        }

        /** @var Scraper $scraper */
        $scraper = resolve(match ($contentType) {
            'text/html' => HtmlScraper::class,
            'application/pdf' => PdfScraper::class,
            default => throw new RuntimeException('Unsupported content type.'),
        });

        assert(is_a($scraper, Scraper::class));

        try {
            $scraper->scrape($this->content);
            $this->content->state = ContentState::COMPLETED;
        } catch (MismatchedCanonicalUrl $e) {
            $url = $e->url;
            goto restart;
        } finally {
            $this->content->save();
        }
    }

    public function failed(Throwable $e): void
    {
        Model::unguarded(function () use ($e) {
            $this->content->update([
                'state' => ContentState::FAILED,
                'error' => $e->getMessage(),
            ]);
        });
    }

    protected function http(): PendingRequest
    {
        return Http::maxRedirects(3)
            ->withMiddleware(static function (callable $handler): callable {
                /** @var CookieJar $jar */
                $jar = Cache::rememberForever('scrape:cookies', fn () => new CookieJar());

                return static function (RequestInterface $request, array $options) use ($handler, $jar) {
                    return $handler($jar->withCookieHeader($request), $options)->then(function (ResponseInterface $response) use ($jar, $request) {
                        $jar->extractCookies($request, $response);
                        Cache::forever('scrape:cookies', $jar);

                        return $response;
                    });
                };
            })
            ->withUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:122.0) Gecko/20100101 Firefox/122.0');
    }
}
