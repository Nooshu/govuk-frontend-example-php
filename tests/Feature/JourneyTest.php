<?php

declare(strict_types=1);

use App\Service\Application;
use App\Service\Journey;

it('serves health and robots', function (): void {
    $this->get('/health')->assertOk()->assertSee('ok');
    $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
});

it('serves the start page with noindex', function (): void {
    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('noindex, nofollow', false);
    $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    $response->assertSee('Apply for a fishing rod licence', false);
    $response->assertSee('href="/licence-length"', false);
});

it('walks the licence journey to confirmation', function (): void {
    $this->get('/')->assertOk();

    $answers = [
        'licence-length' => ['licenceLength' => '1-day'],
        'name' => ['fullName' => 'Sam Smith'],
        'date-of-birth' => ['day' => '1', 'month' => '2', 'year' => '1990'],
        'where-you-will-fish' => ['country' => 'England'],
        'email' => ['email' => 'sam@example.com'],
    ];

    $paths = array_keys($answers);
    foreach ($answers as $step => $payload) {
        $this->get('/'.$step)->assertOk();
        $index = array_search($step, $paths, true);
        $next = $paths[$index + 1] ?? null;
        $expected = $next !== null ? '/'.$next : '/check-answers';
        $this->post('/'.$step, $payload)->assertRedirect($expected);
    }

    $check = $this->get('/check-answers');
    $check->assertOk();
    $check->assertSee('1 day', false);
    $check->assertSee('Sam Smith', false);
    $check->assertSee('1 2 1990', false);
    $check->assertSee('England', false);
    $check->assertSee('Accept and continue', false);

    $this->post('/check-answers')->assertRedirect('/confirmation');
    $confirmation = $this->get('/confirmation');
    $confirmation->assertOk();
    $confirmation->assertSee('Application complete', false);
    $confirmation->assertSee('Your example reference number', false);
    $confirmation->assertSee('Nobody will send you a fishing rod licence', false);
    $confirmation->assertSee('href="/components"', false);
});

it('marks required steps complete in the journey helper', function (): void {
    $app = new Application;
    foreach (Journey::STEPS as $step) {
        $app->markCompleted($step['id']);
    }
    expect(Journey::requiredComplete($app))->toBeTrue();
    expect(Journey::firstIncompleteStep($app))->toBeNull();
    expect(Journey::nextStep('email'))->toBeNull();
    expect(Journey::previousStep('licence-length'))->toBeNull();
    expect(Journey::previousStep('name')['id'])->toBe('licence-length');
    expect(Journey::lengthLabel('12-months'))->toBe('12 months');
    expect(Journey::createReference())->toMatch('/^FR\d{8}$/');
});
