<?php

declare(strict_types=1);

return [
    'service_name' => env('SERVICE_NAME', 'Apply for a fishing rod licence'),
    'frontend_version' => '6.5.1',
    'demos_enabled' => filter_var(
        env('DEMOS_ENABLED', env('APP_ENV') !== 'production' ? 'true' : 'false'),
        FILTER_VALIDATE_BOOLEAN
    ),
    'fixtures_path' => base_path('node_modules/govuk-frontend/dist/govuk/components'),
];
