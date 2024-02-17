<?php

namespace App\Http\Controllers;

use App\Http\Requests\Scrape\ScrapeRequest;
use App\Jobs\Scrape;
use App\Models\Content;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('/scrape')]
class ScrapeController extends Controller
{
    #[Post('/')]
    public function scrape(ScrapeRequest $request): Content
    {
        $content = Content::create(
            attributes: ['url' => $request->validated('url')],
        );

        dispatch(new Scrape(
            content: $content,
            force: boolval(
                $request->validated(
                    key: 'force',
                    default: false,
                ),
            ),
        ));

        return $content;
    }
}
