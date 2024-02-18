<?php

namespace App\Concerns;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait CrawlsExternalSources
{
    protected function httpClient(): PendingRequest
    {
        return Http::maxRedirects(3)
            ->withMiddleware(static function (callable $handler): callable {
                /** @var CookieJar $jar */
                $jar = Cache::rememberForever('crawlers:cookies', fn () => new CookieJar());

                return static function (RequestInterface $request, array $options) use ($handler, $jar) {
                    return $handler($jar->withCookieHeader($request), $options)->then(function (ResponseInterface $response) use ($jar, $request) {
                        $jar->extractCookies($request, $response);
                        Cache::forever('crawlers:cookies', $jar);

                        return $response;
                    });
                };
            })
            ->withUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:122.0) Gecko/20100101 Firefox/122.0');
    }

    protected function contentType(Response|ResponseInterface $response): string
    {
        $header = $response instanceof Response
            ? $response->header('content-type')
            : $response->getHeaderLine('content-type');

        return Str::of($header)
            ->before(';')
            ->trim()
            ->value();
    }
}
