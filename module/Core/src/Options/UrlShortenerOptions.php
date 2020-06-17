<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Core\Options;

use Laminas\Stdlib\AbstractOptions;

use function Functional\contains;

use const Shlinkio\Shlink\Core\DEFAULT_REDIRECT_STATUS_CODE;

class UrlShortenerOptions extends AbstractOptions
{
    protected $__strictMode__ = false; // phpcs:ignore

    private bool $validateUrl = true;
    private int $redirectStatusCode = DEFAULT_REDIRECT_STATUS_CODE;
    private int $redirectCacheLifetime = 30;

    public function isUrlValidationEnabled(): bool
    {
        return $this->validateUrl;
    }

    protected function setValidateUrl(bool $validateUrl): void
    {
        $this->validateUrl = $validateUrl;
    }

    public function redirectStatusCode(): int
    {
        return $this->redirectStatusCode;
    }

    protected function setRedirectStatusCode(int $redirectStatusCode): void
    {
        $this->redirectStatusCode = $this->normalizeRedirectStatusCode($redirectStatusCode);
    }

    private function normalizeRedirectStatusCode(int $statusCode): int
    {
        return contains([301, 302], $statusCode) ? $statusCode : 302;
    }

    public function redirectCacheLifetime(): int
    {
        return $this->redirectCacheLifetime;
    }

    protected function setRedirectCacheLifetime(int $redirectCacheLifetime): void
    {
        $this->redirectCacheLifetime = $redirectCacheLifetime;
    }
}
