<?php

namespace App\Jobs;

use App\Jobs\Concerns\WorksWithSources;
use App\Models\WebPage;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapeWebPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WorksWithSources;

    public function handle(): void
    {
        $response = $this->http()->get($this->source->canonical_url);
        $page = WebPage::create(['html' => $response->body()]);

        try {
            $dom = new DOMDocument();
            $dom->loadHTML($page->html, LIBXML_NOERROR);

            $xpath = new DOMXPath($dom);
            $meta = [];

            /** @var DOMElement $el */
            foreach ($xpath->query('//head/meta') as $el) {
                if ($el->hasAttribute('property')) {
                    $meta[$el->getAttribute('property')] = $el->getAttribute('content');
                } elseif ($el->hasAttribute('name')) {
                    $meta[$el->getAttribute('name')] = $el->getAttribute('content');
                }
            }

            $page->meta = $meta;
            unset($meta);
        } finally {
            $page->save();
            $this->source->content()->associate($page);
            $this->source->save();
        }
    }
}
