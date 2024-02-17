<?php

namespace App\Scrapers;

use App\Models\Content;
use Illuminate\Support\Facades\Http;

class PdfScraper extends Scraper
{
    public function scrape(Content $content): void
    {
        $response = Http::asJson()
            ->baseUrl(config('services.extractor.base_url'))
            ->post('/pdf', ['url' => url()->route('contents.content', $content)]);

        if ($response->failed()) {
            return;
        }

        $content->meta = $response->json('meta');
        $content->title = $response->json('title');
        $content->body = $response->json('text');
    }
}
