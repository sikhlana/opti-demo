<?php

namespace App\Exceptions;

use RuntimeException;

class MismatchedCanonicalUrl extends RuntimeException
{
    public function __construct(
        public readonly string $url,
    ) {
        parent::__construct();
    }
}
