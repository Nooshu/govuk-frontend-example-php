<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * Trusted HTML that must be emitted without escaping (Nunjucks SafeString / | safe).
 */
final readonly class SafeString
{
    public function __construct(public string $value) {}

    public function __toString(): string
    {
        return $this->value;
    }
}
