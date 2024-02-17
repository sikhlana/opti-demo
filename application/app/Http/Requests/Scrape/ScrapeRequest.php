<?php

namespace App\Http\Requests\Scrape;

use Illuminate\Foundation\Http\FormRequest;

class ScrapeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'active_url',
            ],
            'force' => [
                'sometimes',
                'bool',
            ],
        ];
    }
}
