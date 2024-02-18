<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Support\Facades\Storage;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Prefix('/contents')]
class ContentController extends Controller
{
    #[Get('/{content}')]
    public function show(Content $content): Content
    {
        $content->load('parent', 'images', 'parent.images');

        return $content;
    }

    #[Get('/{content}/content', name: 'contents.content')]
    public function content(Content $content): StreamedResponse
    {
        return Storage::response(
            path: $content->path,
            headers: [
                'Content-Type' => $content->content_type,
            ],
        );
    }
}
