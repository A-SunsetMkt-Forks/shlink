<?php

declare(strict_types=1);

use Shlinkio\Shlink\Core\Config\EnvVars;

return [

    'rabbitmq' => [
        'enabled' => (bool) EnvVars::RABBITMQ_ENABLED->loadFromEnv(),
        'host' => EnvVars::RABBITMQ_HOST->loadFromEnv(),
        'use_ssl' => (bool) EnvVars::RABBITMQ_USE_SSL->loadFromEnv(),
        'port' => (int) EnvVars::RABBITMQ_PORT->loadFromEnv(),
        'user' => EnvVars::RABBITMQ_USER->loadFromEnv(),
        'password' => EnvVars::RABBITMQ_PASSWORD->loadFromEnv(),
        'vhost' => EnvVars::RABBITMQ_VHOST->loadFromEnv(),
    ],

];
