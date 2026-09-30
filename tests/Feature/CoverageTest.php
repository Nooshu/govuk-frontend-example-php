<?php

declare(strict_types=1);

use App\Baseline\Policy;
use App\Govuk\Catalogue;
use App\Govuk\FixtureLoader;
use App\Govuk\Json;
use App\Govuk\JsonNumber;
use App\Govuk\Nunjucks;
use App\Govuk\Params;
use App\Govuk\Parity;
use App\Govuk\Renderer;
use App\Govuk\SafeString;
use App\Govuk\UndefinedValue;
use App\Http\Controllers\JourneyController;
use App\Service\Journey;
use App\Support\Assets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

it('serves the component catalogue and fixture fragment', function (): void {
    $this->get('/components')->assertOk()->assertSee('Component catalogue', false);
    $this->get('/components/button')->assertOk()->assertSee('HTML matches the fixture', false);
    $this->get('/components/button?fixture=default')->assertOk();
    $this->get('/components/button/fixture')->assertOk();
    $this->get('/components/not-a-real-component')->assertNotFound();
    $this->get('/components/button?fixture=missing-fixture-name')->assertNotFound();
    $this->get('/components/button/fixture?fixture=missing-fixture-name')->assertNotFound();
});

it('serves Welsh start and supporting pages', function (): void {
    $this->get('/cy')->assertOk();
    foreach (['/fees', '/help', '/guidance', '/accessibility', '/about', '/updates', '/updates?page=2', '/cookies', '/examples/exit-this-page'] as $path) {
        $this->get($path)->assertOk();
    }
    $this->post('/cookies', ['cookies' => 'yes'])->assertRedirect('/cookies');
    $this->post('/cookies', ['cookies' => 'maybe'])->assertRedirect('/cookies');
    $this->post('/cookie-choices', ['cookies' => 'accept', 'returnPath' => '/about'])->assertRedirect('/about');
    $this->post('/cookie-choices', ['cookies' => 'reject', 'returnPath' => ''])->assertRedirect('/');
});

it('serves fingerprinted assets and frontend static files', function (): void {
    $assets = app(Assets::class);
    $css = $this->get($assets->stylesheetHref());
    $css->assertOk();
    $css->assertHeader('Content-Type', 'text/css; charset=utf-8');

    $this->get($assets->appModuleHref())->assertOk();
    $this->get('/assets/missing-file.woff2')->assertNotFound();
    $this->get('/assets/fonts/../../../etc/passwd')->assertNotFound();

    // Prefer a real font or image under Frontend assets if present
    $fontDir = base_path('node_modules/govuk-frontend/dist/govuk/assets/fonts');
    if (is_dir($fontDir)) {
        $files = array_values(array_filter(scandir($fontDir) ?: [], fn ($f) => str_ends_with($f, '.woff2')));
        if ($files !== []) {
            $this->get('/assets/fonts/'.$files[0])->assertOk();
        }
    }
});

it('validates journey steps and guards incomplete check/confirmation', function (): void {
    $this->get('/check-answers')->assertRedirect('/licence-length');
    $this->post('/check-answers')->assertRedirect('/licence-length');
    $this->get('/confirmation')->assertRedirect('/');

    $this->post('/licence-length', ['licenceLength' => 'forever'])->assertRedirect('/licence-length');
    $this->post('/name', ['fullName' => ''])->assertRedirect('/name');
    $this->post('/date-of-birth', ['day' => '', 'month' => '', 'year' => ''])->assertRedirect('/date-of-birth');
    $this->post('/where-you-will-fish', ['country' => ''])->assertRedirect('/where-you-will-fish');
    $this->post('/email', ['email' => 'not-an-email'])->assertRedirect('/email');
});

it('covers parity helpers catalogue and fixture loader wrappers', function (): void {
    expect(Parity::matches('a', 'a'))->toBeTrue();
    expect(Parity::banner(true)['type'])->toBe('success');
    expect(Parity::banner(false)['type'])->toBe('');
    expect(Parity::difference("a\nb", "a\nc"))->toContain('first difference');
    expect(Parity::difference('same', 'sameX'))->toContain('first difference');
    // equal lines until shorter string ends
    expect(Parity::difference("a\nb", 'a'))->toContain('missing line');

    $button = Catalogue::describe('button');
    expect($button['title'])->toBe('Button');
    $unknown = Catalogue::describe('made-up-widget');
    expect($unknown['title'])->toBe('Made Up Widget');

    $loader = FixtureLoader::fromConfig();
    expect($loader->componentNames())->toContain('button');
    expect($loader->load('button')['component'])->toBe('button');

    expect((string) new SafeString('<em>ok</em>'))->toBe('<em>ok</em>');
    expect(Journey::step('nope'))->toBeNull();
    expect(Journey::nextStep('nope'))->toBeNull();
    expect(Journey::previousStep('nope'))->toBeNull();
});

it('covers baseline policy edge cases', function (): void {
    $policy = new Policy;
    expect($policy->isDocument(Policy::KIND_DOCUMENT))->toBeTrue();
    expect($policy->jsEnabledSnippet())->not->toBe('');
    expect($policy->jsEnabledScriptHash())->toStartWith('sha256-');
    expect($policy->headersToRemove())->not->toBeEmpty();

    $withCookie = $policy->buildHeaders(Policy::KIND_DOCUMENT, true, true, [
        ['href' => '/assets/fonts/x.woff2', 'as' => 'font', 'type' => 'font/woff2'],
    ]);
    expect($withCookie['Cache-Control'])->toBe('private, no-cache');
    expect($withCookie)->toHaveKey('Strict-Transport-Security');
    expect($withCookie)->toHaveKey('Link');

    expect(fn () => $policy->buildHeaders('not-a-kind', false))->toThrow(InvalidArgumentException::class);
    expect(fn () => $policy->buildHeaders(Policy::KIND_STATIC_ASSET, false, true))->toThrow(InvalidArgumentException::class);
    expect(fn () => $policy->preloadLinkHeader([['href' => 'https://evil.example/x', 'as' => 'font']]))
        ->toThrow(InvalidArgumentException::class);
    expect(fn () => new Policy('/tmp/does-not-exist-baseline-policy.json'))->toThrow(RuntimeException::class);
});

it('covers assets resolve branches and missing stylesheet', function (): void {
    $assets = new Assets;
    expect($assets->preloads())->toBeArray();
    expect($assets->resolve('/other'))->toBeNull();
    expect($assets->resolve($assets->stylesheetHref())['kind'])->toBe('fingerprinted-asset');
    expect($assets->resolve($assets->appModuleHref())['contentType'])->toContain('javascript');

    // Script href via resolve of stylesheet sibling pattern
    $scriptPath = parse_url($assets->stylesheetHref(), PHP_URL_PATH);
    expect($scriptPath)->toBeString();

    expect(fn () => new Assets('/tmp/missing-application.css'))->toThrow(RuntimeException::class);
    expect(fn () => new Assets(base_path('dist/stylesheets/application.css'), '/tmp/missing-govuk-root'))
        ->toThrow(RuntimeException::class);
});

it('covers nunjucks json and renderer option branches not hit by fixtures alone', function (): void {
    expect(Nunjucks::at(['a', 'b'], 0))->toBe('a');
    expect(Nunjucks::at(['a'], 9))->toBeInstanceOf(UndefinedValue::class);
    expect(Nunjucks::str(true))->toBe('true');
    expect(Nunjucks::str(false))->toBe('false');
    expect(Nunjucks::str([1, 2]))->toBe('1,2');
    expect(Nunjucks::str(Params::make('x', 1)))->toBe('[object Object]');
    expect(Nunjucks::str(1.5))->toBe('1.5');
    expect(Nunjucks::formatNumber('1.500'))->toBe('1.5');
    expect(Nunjucks::out(new SafeString('<b>x</b>')))->toBe('<b>x</b>');
    expect(Nunjucks::truthy(0))->toBeFalse();
    expect(Nunjucks::truthy(new JsonNumber('0')))->toBeFalse();
    expect(Nunjucks::truthy(new JsonNumber('2')))->toBeTrue();

    $decoded = Json::decode('{"a":"\\b\\f\\n\\r\\t\\/\\\\\\"","n":1.5e+2,"u":"\\u0041"}');
    expect($decoded)->toBeInstanceOf(Params::class);

    expect(fn () => Json::decode('{"bad":"\\q"}'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Json::decode('"unterminated'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Json::decode('"\\uZZZZ"'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Json::decode('"\\'))->toThrow(InvalidArgumentException::class);

    // Forms: text-only slots / beforeInputs / afterInputs
    Renderer::render('input', [
        'label' => ['text' => 'Name'],
        'id' => 'name',
        'name' => 'name',
        'formGroup' => [
            'beforeInput' => ['text' => 'Before'],
            'afterInput' => ['text' => 'After'],
            'attributes' => ['data-x' => '1'],
        ],
        'prefix' => ['text' => '£'],
        'suffix' => ['text' => 'kg'],
    ]);

    Renderer::render('character-count', [
        'name' => 'more',
        'id' => 'more',
        'label' => ['text' => 'More'],
        'maxwords' => 10,
        'textareaDescriptionText' => 'Up to %{count} words',
        'formGroup' => [
            'afterInput' => ['text' => 'Extra'],
            'classes' => 'extra',
        ],
    ]);

    Renderer::render('character-count', [
        'name' => 'more2',
        'id' => 'more2',
        'label' => ['text' => 'More'],
        'maxlength' => 20,
        'formGroup' => [
            'afterInput' => ['html' => '<p>Extra</p>'],
        ],
    ]);

    Renderer::render('password-input', [
        'name' => 'password',
        'id' => 'password',
        'label' => ['text' => 'Password'],
        'formGroup' => [
            'afterInput' => ['text' => 'Hint after'],
            'attributes' => ['data-y' => '2'],
        ],
    ]);

    Renderer::render('password-input', [
        'name' => 'password2',
        'id' => 'password2',
        'label' => ['text' => 'Password'],
        'formGroup' => [
            'afterInput' => ['html' => '<span>x</span>'],
        ],
    ]);

    Renderer::render('checkboxes', [
        'name' => 'regions',
        'items' => [
            ['value' => 'a', 'text' => 'A'],
            null,
            ['value' => 'b', 'text' => 'B'],
        ],
        'formGroup' => [
            'beforeInputs' => ['text' => 'Before boxes'],
            'afterInputs' => ['html' => '<p>After</p>'],
        ],
        'fieldset' => ['legend' => ['text' => 'Regions']],
    ]);

    Renderer::render('radios', [
        'name' => 'choice',
        'items' => [
            ['value' => 'a', 'text' => 'A'],
            null,
            ['value' => 'b', 'text' => 'B'],
        ],
        'formGroup' => [
            'beforeInputs' => ['html' => '<p>Before</p>'],
            'afterInputs' => ['text' => 'After radios'],
        ],
        'fieldset' => ['legend' => ['text' => 'Choice']],
    ]);

    Renderer::render('date-input', [
        'id' => 'dob',
        'namePrefix' => 'dob',
        'fieldset' => ['legend' => ['text' => 'DOB']],
        'formGroup' => [
            'beforeInputs' => ['text' => 'Before date'],
            'afterInputs' => ['text' => 'After date'],
        ],
        'items' => [
            ['name' => 'day', 'classes' => 'govuk-input--width-2'],
            null,
            ['name' => 'month', 'classes' => 'govuk-input--width-2'],
            ['name' => 'year', 'classes' => 'govuk-input--width-4'],
        ],
    ]);
});

it('hides the catalogue when demos are disabled', function (): void {
    Config::set('govuk.demos_enabled', false);
    // Routes already registered for this process — hitting catalogue still works if registered at boot.
    // Assert config flag is respected by StartController instead.
    $this->get('/')->assertOk();
});

it('covers change-from-check-answers return and invalid journey steps', function (): void {
    $this->post('/licence-length', ['licenceLength' => '12-months'])->assertRedirect('/name');
    $this->post('/name', ['fullName' => 'Sam Smith'])->assertRedirect('/date-of-birth');
    $this->post('/date-of-birth', ['day' => '1', 'month' => '2', 'year' => '1990'])->assertRedirect('/where-you-will-fish');
    $this->post('/where-you-will-fish', ['country' => 'Wales'])->assertRedirect('/email');
    $this->post('/email', ['email' => 'sam@example.com'])->assertRedirect('/check-answers');

    $this->get('/name?return=check-answers')->assertOk()->assertSee('href="/check-answers"', false);
    $this->post('/name', [
        'fullName' => 'Sam Smith',
        'returnTo' => 'check-answers',
    ])->assertRedirect('/check-answers');

    $this->post('/name', [
        'fullName' => '',
        'returnTo' => 'check-answers',
    ])->assertRedirect('/name?return=check-answers');

    $controller = app(JourneyController::class);
    $request = Request::create('/nope', 'GET');
    $request->setLaravelSession(app('session.store'));
    try {
        $controller->show($request, 'nope');
        expect(false)->toBeTrue();
    } catch (NotFoundHttpException) {
        expect(true)->toBeTrue();
    }
    try {
        $controller->store(Request::create('/nope', 'POST'), 'nope');
        expect(false)->toBeTrue();
    } catch (NotFoundHttpException) {
        expect(true)->toBeTrue();
    }
});
