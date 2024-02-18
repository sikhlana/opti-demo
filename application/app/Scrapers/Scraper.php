<?php

namespace App\Scrapers;

use App\Models\Content;
use Intervention\Validation\Rules\DataUri;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Sikhlana\Singleton\Singleton;

abstract class Scraper implements Singleton
{
    private static ?DataUri $dataUriValidator = null;

    abstract public function scrape(Content $content): void;

    protected function saveImage(Content $content, string|StreamInterface $file, ?string $contentType = null, ?string $source = null): void
    {
        if (is_null(self::$dataUriValidator)) {
            self::$dataUriValidator = new DataUri([
                'image/gif',
                'image/jpeg',
                'image/png',
                'image/webp',
            ]);
        }

        if (is_string($file)) {
            if (! self::$dataUriValidator->isValid($file)) {
                throw new InvalidArgumentException('Given string is not a valid data-uri.');
            }

            $file = fopen("data://{$file}", 'rb');
            $contentType = stream_get_meta_data($file)['mediatype'];
            $source = "data-uri:{$content->url}";
        }

        $image = $content->images()->create([
            'source' => $source,
            'content_type' => $contentType,
        ]);

        $image->data = $file;
    }
}
