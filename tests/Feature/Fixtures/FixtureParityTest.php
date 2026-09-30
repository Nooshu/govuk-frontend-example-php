<?php

declare(strict_types=1);

use App\Govuk\FixtureLoader;
use App\Govuk\Parity;
use App\Govuk\Renderer;

/**
 * Byte-for-byte parity: PHP Render output must equal every official fixtures.json html.
 */
function govukComponentsDir(): string
{
    $root = dirname(__DIR__, 3);
    $dir = $root.'/node_modules/govuk-frontend/dist/govuk/components';
    if (! is_dir($dir)) {
        throw new RuntimeException('GOV.UK Frontend is not installed, run `npm install`: missing '.$dir);
    }

    return $dir;
}

test('render matches every official fixture', function () {
    $root = govukComponentsDir();
    $components = FixtureLoader::fixtureComponents($root);
    expect($components)->not->toBeEmpty();

    $total = 0;
    $passed = 0;
    foreach ($components as $component) {
        $set = FixtureLoader::loadFixtures($root, $component);
        $total += count($set['fixtures']);

        foreach ($set['fixtures'] as $fixture) {
            $got = Renderer::render($component, $fixture['options']);
            if (! Parity::matches($got, $fixture['html'])) {
                expect($got)->toBe(
                    $fixture['html'],
                    $component.' / '.$fixture['name']."\n".Parity::difference($fixture['html'], $got)
                );
            }
            $passed++;
        }
    }

    expect($total)->toBeGreaterThan(0);
    expect($passed)->toBe($total); // e.g. 716/716 fixtures
})->group('FixtureParity');

test('components cover every fixture component', function () {
    $root = govukComponentsDir();
    $shipped = FixtureLoader::fixtureComponents($root);
    $supported = array_fill_keys(Renderer::components(), true);
    foreach ($shipped as $name) {
        expect($supported)->toHaveKey($name, 'component "'.$name.'" ships fixtures but has no PHP renderer');
    }
})->group('FixtureParity');

test('render rejects unknown component', function () {
    expect(fn () => Renderer::render('not-a-component', null))
        ->toThrow(InvalidArgumentException::class);
})->group('FixtureParity');
