<?php

declare(strict_types=1);

namespace App\Govuk;

/**
 * Sentinel for a missing option (JavaScript / Nunjucks undefined).
 * Distinct from null (explicit JSON null).
 */
final class UndefinedValue
{
    private static ?self $instance = null;

    private function __construct() {}

    public static function instance(): self
    {
        return self::$instance ??= new self;
    }
}
