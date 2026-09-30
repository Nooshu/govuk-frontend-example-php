<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * JSON number whose original spelling is preserved for JS stringification parity.
 */
final readonly class JsonNumber
{
    public function __construct(public string $value) {}
}
