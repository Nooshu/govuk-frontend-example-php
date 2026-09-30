<?php

declare(strict_types=1);

namespace App\Providers;

use App\Baseline\Policy;
use App\Support\Assets;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Policy::class, fn () => new Policy);
        $this->app->singleton(Assets::class, fn () => new Assets);
    }

    public function boot(): void
    {
        View::composer('layouts.govuk', function ($view): void {
            /** @var Assets $assets */
            $assets = app(Assets::class);
            /** @var Policy $policy */
            $policy = app(Policy::class);
            $view->with([
                'stylesheetHref' => $assets->stylesheetHref(),
                'appModuleHref' => $assets->appModuleHref(),
                'jsEnabledSnippet' => $policy->jsEnabledSnippet(),
                'serviceName' => config('govuk.service_name'),
                'frontendVersion' => config('govuk.frontend_version'),
            ]);
        });
    }
}
