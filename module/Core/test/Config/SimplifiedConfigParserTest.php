<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Core\Config;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Core\Config\SimplifiedConfigParser;

use function array_merge;

class SimplifiedConfigParserTest extends TestCase
{
    private SimplifiedConfigParser $postProcessor;

    public function setUp(): void
    {
        $this->postProcessor = new SimplifiedConfigParser();
    }

    /** @test */
    public function properlyMapsSimplifiedConfig(): void
    {
        $config = [
            'tracking' => [
                'disable_track_param' => 'foo',
            ],

            'entity_manager' => [
                'connection' => [
                    'driver' => 'mysql',
                    'host' => 'shlink_db_mysql',
                    'port' => '3306',
                ],
            ],
        ];
        $simplified = [
            'disable_track_param' => 'bar',
            'short_domain_schema' => 'https',
            'short_domain_host' => 'doma.in',
            'validate_url' => true,
            'delete_short_url_threshold' => 50,
            'invalid_short_url_redirect_to' => 'foobar.com',
            'regular_404_redirect_to' => 'bar.com',
            'base_url_redirect_to' => 'foo.com',
            'redis_servers' => [
                'tcp://1.1.1.1:1111',
                'tcp://1.2.2.2:2222',
            ],
            'db_config' => [
                'dbname' => 'shlink',
                'user' => 'foo',
                'password' => 'bar',
                'port' => '1234',
            ],
            'base_path' => '/foo/bar',
            'task_worker_num' => 50,
            'visits_webhooks' => [
                'http://my-api.com/api/v2.3/notify',
                'https://third-party.io/foo',
            ],
            'default_short_codes_length' => 8,
            'geolite_license_key' => 'kjh23ljkbndskj345',
            'mercure_public_hub_url' => 'public_url',
            'mercure_internal_hub_url' => 'internal_url',
            'mercure_jwt_secret' => 'super_secret_value',
            'anonymize_remote_addr' => false,
            'redirect_status_code' => 301,
            'redirect_cache_lifetime' => 90,
            'port' => 8888,
        ];
        $expected = [
            'tracking' => [
                'disable_track_param' => 'bar',
                'anonymize_remote_addr' => false,
            ],

            'entity_manager' => [
                'connection' => [
                    'driver' => 'mysql',
                    'host' => 'shlink_db_mysql',
                    'dbname' => 'shlink',
                    'user' => 'foo',
                    'password' => 'bar',
                    'port' => '1234',
                ],
            ],

            'url_shortener' => [
                'domain' => [
                    'schema' => 'https',
                    'hostname' => 'doma.in',
                ],
                'validate_url' => true,
                'visits_webhooks' => [
                    'http://my-api.com/api/v2.3/notify',
                    'https://third-party.io/foo',
                ],
                'default_short_codes_length' => 8,
                'redirect_status_code' => 301,
                'redirect_cache_lifetime' => 90,
            ],

            'delete_short_urls' => [
                'visits_threshold' => 50,
                'check_visits_threshold' => true,
            ],

            'dependencies' => [
                'aliases' => [
                    'lock_store' => 'redis_lock_store',
                ],
            ],

            'cache' => [
                'redis' => [
                    'servers' => [
                        'tcp://1.1.1.1:1111',
                        'tcp://1.2.2.2:2222',
                    ],
                ],
            ],

            'router' => [
                'base_path' => '/foo/bar',
            ],

            'not_found_redirects' => [
                'invalid_short_url' => 'foobar.com',
                'regular_404' => 'bar.com',
                'base_url' => 'foo.com',
            ],

            'mezzio-swoole' => [
                'swoole-http-server' => [
                    'port' => 8888,
                    'options' => [
                        'task_worker_num' => 50,
                    ],
                ],
            ],

            'geolite2' => [
                'license_key' => 'kjh23ljkbndskj345',
            ],

            'mercure' => [
                'public_hub_url' => 'public_url',
                'internal_hub_url' => 'internal_url',
                'jwt_secret' => 'super_secret_value',
            ],
        ];

        $result = ($this->postProcessor)(array_merge($config, $simplified));

        self::assertEquals(array_merge($expected, $simplified), $result);
    }
}
