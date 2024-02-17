<?php

namespace App\Scrapers;

use App\Models\Content;
use Sikhlana\Singleton\Singleton;

abstract class Scraper implements Singleton
{
    abstract public function scrape(Content $content): void;
}
