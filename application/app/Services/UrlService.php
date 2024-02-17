<?php

namespace App\Services;

use App\Support\TrackingParams;
use League\Uri\Uri;
use Sikhlana\Singleton\Singleton;

class UrlService implements Singleton
{
    public function __construct(
        protected TrackingParams $params,
    ) {
    }

    public function normalize(string $url): string
    {
        $uri = Uri::new($url)
            ->withFragment(null);

        if (! is_null($uri->getQuery())) {
            parse_str($uri->getQuery(), $query);

            $query = array_filter(
                $query,
                fn ($key) => ! in_array($key, $this->params->keys),
                ARRAY_FILTER_USE_KEY,
            );

            $uri = $uri->withQuery(empty($query) ? null : http_build_query($query));
        }

        return $uri->toString();
    }
}
