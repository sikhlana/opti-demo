<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Prefix('/images')]
class ImageController extends Controller
{
    #[Get('/{image}', name: 'images.show')]
    public function show(Image $image): StreamedResponse
    {
        return Storage::response($image->path, headers: [
            'Content-Type' => $image->content_type,
            'Content-Length' => $image->size,
            'Content-Digest' => 'sha-256='.base64_encode(hex2bin($image->hash)),
        ]);
    }
}
