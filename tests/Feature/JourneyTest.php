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
});

it('walks the licence journey to confirmation', function (): void {
    $this->get('/')->assertOk();
    $this->get('/task-list')->assertOk();

    $answers = [
        'name' => ['firstName' => 'Sam', 'lastName' => 'Smith'],
        'date-of-birth' => ['day' => '1', 'month' => '2', 'year' => '1990'],
        'email' => ['email' => 'sam@example.com'],
        'contact-preference' => ['contactBy' => 'email'],
        'where-you-will-fish' => ['regions' => ['england']],
        'licence-length' => ['licenceLength' => '1-day'],
        'start-month' => ['startMonth' => 'March 2026'],
        'address' => [
            'addressLine1' => '1 High Street',
            'addressLine2' => '',
            'town' => 'Bristol',
            'postcode' => 'BS1 1AA',
        ],
        'evidence' => [],
        'additional-details' => ['additionalDetails' => ''],
        'create-a-password' => ['password' => 'password1', 'confirmPassword' => 'password1'],
    ];

    foreach ($answers as $step => $payload) {
        $this->get('/'.$step)->assertOk();
        $this->post('/'.$step, $payload)->assertRedirect('/task-list');
    }

    $this->get('/check-answers')->assertOk();
    $this->post('/check-answers')->assertRedirect('/confirmation');
    $this->get('/confirmation')->assertOk()->assertSee('Application complete');
});

it('marks required steps complete in the journey helper', function (): void {
    $app = new Application;
    foreach (Journey::STEPS as $step) {
        if ($step['optional']) {
            continue;
        }
        $app->markCompleted($step['id']);
    }
    expect(Journey::requiredComplete($app))->toBeTrue();
});
