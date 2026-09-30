<?php

declare(strict_types=1);

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\JourneyController;
use App\Http\Controllers\StartController;
use App\Http\Controllers\StaticPageController;
use App\Http\Middleware\BaselineHeaders;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);
Route::get('/robots.txt', fn () => response("User-agent: *\nDisallow: /\n", 200, [
    'Content-Type' => 'text/plain; charset=utf-8',
    'X-Robots-Tag' => 'noindex, nofollow',
]));
Route::get('/assets/{path}', [AssetController::class, 'show'])->where('path', '.*');

Route::middleware([BaselineHeaders::class])->group(function (): void {
    Route::get('/', [StartController::class, 'english']);
    Route::get('/cy', [StartController::class, 'welsh']);

    Route::get('/fees', [StaticPageController::class, 'fees']);
    Route::get('/help', [StaticPageController::class, 'help']);
    Route::get('/guidance', [StaticPageController::class, 'guidance']);
    Route::get('/accessibility', [StaticPageController::class, 'accessibility']);
    Route::get('/about', [StaticPageController::class, 'about']);
    Route::get('/updates', [StaticPageController::class, 'updates']);
    Route::get('/cookies', [StaticPageController::class, 'cookies']);
    Route::post('/cookies', [StaticPageController::class, 'saveCookies']);
    Route::post('/cookie-choices', [StaticPageController::class, 'cookieChoices']);
    Route::get('/examples/exit-this-page', [StaticPageController::class, 'exitThisPage']);

    Route::get('/task-list', [JourneyController::class, 'taskList']);
    Route::get('/check-answers', [JourneyController::class, 'checkAnswers']);
    Route::post('/check-answers', [JourneyController::class, 'submitAnswers']);
    Route::get('/confirmation', [JourneyController::class, 'confirmation']);

    Route::get('/{step}', [JourneyController::class, 'show'])
        ->where('step', 'name|date-of-birth|email|contact-preference|where-you-will-fish|licence-length|start-month|address|evidence|additional-details|create-a-password');
    Route::post('/{step}', [JourneyController::class, 'store'])
        ->where('step', 'name|date-of-birth|email|contact-preference|where-you-will-fish|licence-length|start-month|address|evidence|additional-details|create-a-password');
});

if (config('govuk.demos_enabled')) {
    Route::middleware([BaselineHeaders::class])->group(function (): void {
        Route::get('/components', [CatalogueController::class, 'index']);
        Route::get('/components/{name}', [CatalogueController::class, 'show']);
        Route::get('/components/{name}/fixture', [CatalogueController::class, 'fixture']);
    });
}
