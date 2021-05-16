<?php

declare(strict_types=1);

use const Shlinkio\Shlink\Core\DEFAULT_REDIRECT_CACHE_LIFETIME;
use const Shlinkio\Shlink\Core\DEFAULT_REDIRECT_STATUS_CODE;
use const Shlinkio\Shlink\Core\DEFAULT_SHORT_CODES_LENGTH;

return [

    'url_shortener' => [
        'domain' => [
            'schema' => 'https',
            'hostname' => '',
        ],
        'validate_url' => false, // Deprecated
        'visits_webhooks' => [],
        'default_short_codes_length' => DEFAULT_SHORT_CODES_LENGTH,
        'redirect_status_code' => DEFAULT_REDIRECT_STATUS_CODE,
        'redirect_cache_lifetime' => DEFAULT_REDIRECT_CACHE_LIFETIME,
        'auto_resolve_titles' => false,
    ],

];
