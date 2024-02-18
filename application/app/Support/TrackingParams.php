<?php

namespace App\Support;

use RuntimeException;
use Sikhlana\Singleton\Singleton;

/**
 * Loads all tracking query params, to be used later, from a CSV file.
 *
 * @see https://github.com/mpchadwick/tracking-query-params-registry
 */
readonly class TrackingParams implements Singleton
{
    public array $keys;

    public function __construct()
    {
        $params = [];
        $handle = fopen(resource_path('tracking_params.csv'), 'r');

        if (! $handle) {
            throw new RuntimeException('Unable to read tracking parameters data file.');
        }

        fgets($handle); // Skip the headers.

        while (! feof($handle)) {
            $data = fgetcsv($handle);

            if (! $data) {
                continue; // Maybe an empty line?
            }

            $params[] = $data[0];
        }

        fclose($handle);
        $this->keys = $params;
    }
}
