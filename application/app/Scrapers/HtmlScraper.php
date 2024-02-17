<?php

namespace App\Scrapers;

use App\Exceptions\MismatchedCanonicalUrl;
use App\Models\Content;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Http;

class HtmlScraper extends Scraper
{
    public function scrape(Content $content): void
    {
        $dom = new DOMDocument();

        $dom->loadHTML(
            mb_convert_encoding(stream_get_contents($content->content), 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_NOERROR
        );

        $xpath = new DOMXPath($dom);

        if (($url = $this->canonical($xpath)) && ($content->canonical_url !== $url)) {
            throw new MismatchedCanonicalUrl($url);
        }

        $content->meta = $this->meta($xpath);
        $content->title = $this->title($xpath, $content->meta);
        $content->body = $this->body($content, $content->meta);
    }

    protected function canonical(DOMXPath $xpath): ?string
    {
        /** @var DOMElement $el */
        foreach ($xpath->query("//head/link[@rel='canonical']") as $el) {
            return $el->getAttribute('href');
        }

        /** @var DOMElement $el */
        foreach ($xpath->query("//head/meta[@property='og:url']") as $el) {
            return $el->getAttribute('content');
        }

        return null;
    }

    protected function meta(DOMXPath $xpath): array
    {
        $meta = [];
        $tags = [];

        /** @var DOMElement $el */
        foreach ($xpath->query('//head/meta[@name]') as $el) {
            $tags[$el->getAttribute('name')] = $el->getAttribute('content');
        }

        /** @var DOMElement $el */
        foreach ($xpath->query('//head/meta[@property]') as $el) {
            $tags[$el->getAttribute('property')] = $el->getAttribute('content');
        }

        $meta['tags'] = $tags;
        $jsonld = [];

        /** @var DOMElement $el */
        foreach ($xpath->query("//script[@type='application/ld+json']") as $el) {
            if (empty($json = json_decode(mb_convert_encoding($el->textContent, 'UTF-8', 'HTML-ENTITIES'), false))) {
                continue;
            }

            if (array_is_list($json = (array) $json)) {
                array_push($jsonld, ...$json);
            } else {
                $jsonld[] = $json;
            }
        }

        $meta['jsonld'] = $jsonld;

        return $meta;
    }

    protected function title(DOMXPath $xpath, array $meta): ?string
    {
        foreach ($meta['jsonld'] as $ld) {
            if (@$ld['@type'] === 'NewsArticle' && isset($ld['headline'])) {
                return $ld['headline'];
            }
        }

        if (isset($meta['tags']['og:title'])) {
            return $meta['tags']['og:title'];
        }

        /** @var DOMElement $el */
        foreach ($xpath->query('//head/title') as $el) {
            return mb_convert_encoding($el->textContent, 'UTF-8', 'HTML-ENTITIES');
        }

        return null;
    }

    protected function body(Content $content, array $meta): ?string
    {
        foreach ($meta['jsonld'] as $ld) {
            if (@$ld['@type'] === 'NewsArticle' && isset($ld['articleBody'])) {
                return $ld['articleBody'];
            }
        }

        $response = Http::asJson()
            ->baseUrl(config('services.extractor.base_url'))
            ->post('/html', ['url' => url()->route('contents.content', $content)]);

        if ($response->ok()) {
            return $response->json('text');
        }

        return null;
    }
}
