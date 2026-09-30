<?php

declare(strict_types=1);

use App\Govuk\Attributes;
use App\Govuk\Components\Forms;
use App\Govuk\Components\Text;
use App\Govuk\FixtureLoader;
use App\Govuk\Json;
use App\Govuk\JsonNumber;
use App\Govuk\Nunjucks;
use App\Govuk\Params;
use App\Govuk\Parity;
use App\Govuk\Renderer;
use App\Govuk\SafeString;
use App\Support\Assets;

it('covers remaining helper and component edge branches', function (): void {
    expect(Attributes::render(new SafeString(' data-x="1"')))->toBe(' data-x="1"');
    expect(Attributes::i18n('key', null, 'not-params'))->toBe('');
    expect(Attributes::i18n('key', 'Hello', null))->toContain('data-i18n.key');
    expect(Attributes::render(Params::make('data-safe', new SafeString('ok'))))->toContain('data-safe="ok"');

    expect(Renderer::mustRender('tag', ['text' => 'New']))->toContain('govuk-tag');
    expect(Text::renderTag(null))->toContain('govuk-tag');

    expect(Params::make('a', 1)->has('a'))->toBeTrue();
    expect(iterator_to_array(Params::make('a', 1)))->toBe(['a']);
    expect(fn () => Params::make('only-one'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Params::make(1, 'x'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Params::fromJson('[1,2]'))->toThrow(InvalidArgumentException::class);
    expect(Params::fromJson('{"a":1}')->get('a'))->toBeInstanceOf(JsonNumber::class);

    expect(Parity::difference('x', 'y'))->toContain('first difference');

    expect(Nunjucks::indent('', 2, true))->toBe('');
    expect(Nunjucks::length(true))->toBe(0);
    expect(Nunjucks::length('ab'))->toBe(2);
    expect(Nunjucks::length(new SafeString('xy')))->toBe(2);
    expect(Nunjucks::length(Params::make('a', 1)))->toBe(1);
    expect(Nunjucks::length((object) []))->toBe(0);
    expect(Nunjucks::looseEq(true, new JsonNumber('1')))->toBeTrue();
    expect(Nunjucks::looseEq(new JsonNumber('1'), false))->toBeFalse();
    expect(Nunjucks::looseEq(new JsonNumber('1'), '1'))->toBeTrue();
    expect(Nunjucks::looseEq('1', new JsonNumber('1')))->toBeTrue();
    expect(Nunjucks::looseEq('a', 'a'))->toBeTrue();
    expect(Nunjucks::strictEq(null, null))->toBeTrue();
    expect(Nunjucks::strictEq(new JsonNumber('1'), '1'))->toBeFalse();
    expect(Nunjucks::strictEq(new JsonNumber('1'), new JsonNumber('1')))->toBeTrue();
    expect(Nunjucks::strictEq(true, true))->toBeTrue();
    expect(Nunjucks::strictEq('a', 'a'))->toBeTrue();
    expect(Nunjucks::strictEq(1.0, 1.0))->toBeTrue();
    expect(Nunjucks::strictEq([], []))->toBeTrue();
    expect(Nunjucks::looseEq(new JsonNumber('0'), '   '))->toBeTrue();
    expect(Nunjucks::looseEq(new JsonNumber('1'), 'abc'))->toBeFalse();
    expect(Nunjucks::contains('a', new SafeString('cat')))->toBeTrue();
    expect(Nunjucks::contains('a', Params::make('a', 1)))->toBeTrue();
    expect(Nunjucks::contains('z', Params::make('a', 1)))->toBeFalse();
    expect(Nunjucks::contains('x', 123))->toBeFalse();
    expect(Nunjucks::heading('9', '2'))->toBe('2');
    expect(Nunjucks::heading(3, '2'))->toBe('3');

    expect(fn () => Json::decode('{1:2}'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Json::decode('{"a" 2}'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Json::decode('{"a":1 "b":2}'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Json::decode('[1 2]'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Json::decode(''))->toThrow(InvalidArgumentException::class);
    expect(fn () => Json::decode('@'))->toThrow(InvalidArgumentException::class);
    expect(Json::decode('-12.5e-1'))->toBeInstanceOf(JsonNumber::class);

    $tmp = sys_get_temp_dir().'/govuk-fixtures-'.uniqid();
    mkdir($tmp);
    expect(fn () => FixtureLoader::fixtureComponents($tmp.'/nope'))->toThrow(RuntimeException::class);
    expect(fn () => FixtureLoader::loadFixtures($tmp, 'missing'))->toThrow(RuntimeException::class);
    mkdir($tmp.'/badcomp');
    file_put_contents($tmp.'/badcomp/fixtures.json', '[]');
    expect(fn () => FixtureLoader::loadFixtures($tmp, 'badcomp'))->toThrow(InvalidArgumentException::class);
    mkdir($tmp.'/obj');
    file_put_contents($tmp.'/obj/fixtures.json', '{"fixtures":[null,{"name":"x","options":null,"hidden":"no","html":null}]}');
    $set = FixtureLoader::loadFixtures($tmp, 'obj');
    expect($set['fixtures'][0]['name'])->toBe('x');
    expect($set['component'])->toBe('obj');
    rmdir_recursive($tmp);

    Renderer::render('textarea', [
        'name' => 'notes',
        'id' => 'notes',
        'label' => ['text' => 'Notes'],
        'formGroup' => [
            'beforeInput' => ['text' => 'Before ta'],
            'afterInput' => ['html' => '<p>After ta</p>'],
        ],
    ]);
    Renderer::render('select', [
        'name' => 'sel',
        'id' => 'sel',
        'label' => ['text' => 'Select'],
        'items' => [['value' => '1', 'text' => 'One']],
        'formGroup' => [
            'beforeInput' => ['html' => '<p>Before sel</p>'],
            'afterInput' => ['text' => 'After sel'],
        ],
    ]);
    Renderer::render('file-upload', [
        'name' => 'file',
        'id' => 'file',
        'label' => ['text' => 'File'],
        'formGroup' => [
            'beforeInput' => ['text' => 'Before file'],
            'afterInput' => ['text' => 'After file'],
        ],
    ]);
    Renderer::render('footer', [
        'navigation' => [[
            'title' => 'Nav',
            'columns' => 2,
            'items' => [
                ['href' => '/ok', 'text' => 'OK'],
                ['text' => 'Missing href'],
                ['href' => '/x'],
            ],
        ]],
    ]);
    Renderer::render('pagination', [
        'previous' => ['href' => '/p', 'html' => '<strong>Prev</strong>'],
        'next' => ['href' => '/n', 'text' => 'Next'],
        'items' => [['number' => 1, 'href' => '/1', 'current' => true]],
    ]);
    Renderer::render('summary-list', [
        'card' => ['title' => ['html' => '<em>Card</em>']],
        'rows' => [[
            'key' => ['text' => 'Name'],
            'value' => ['text' => 'Sam'],
            'actions' => ['items' => [['href' => '/change', 'text' => 'Change', 'visuallyHiddenText' => 'name']]],
        ]],
    ]);
    $ref = new ReflectionClass(Forms::class);
    $cap = $ref->getMethod('capitalise');
    $cap->setAccessible(true);
    expect($cap->invoke(null, ''))->toBe('');

    $assetRoot = sys_get_temp_dir().'/govuk-assets-'.uniqid();
    mkdir($assetRoot);
    foreach ([
        'a.css' => 'body{}',
        'a.js' => '1',
        'a.mjs' => '1',
        'a.woff' => 'x',
        'a.svg' => '<svg/>',
        'a.png' => 'x',
        'a.ico' => 'x',
        'a.json' => '{}',
        'a.bin' => 'x',
        'finger-abcdef12-print.txt' => 'x',
    ] as $name => $body) {
        file_put_contents($assetRoot.'/'.$name, $body);
    }
    $assets = new Assets(base_path('dist/stylesheets/application.css'), base_path('node_modules/govuk-frontend/dist/govuk'), $assetRoot);
    $r = new ReflectionClass($assets);
    $prop = $r->getProperty('scriptHref');
    $prop->setAccessible(true);
    $scriptHref = $prop->getValue($assets);
    expect($assets->resolve($scriptHref)['contentType'])->toContain('javascript');
    expect($assets->resolve('/assets/a.css')['contentType'])->toContain('css');
    expect($assets->resolve('/assets/a.js')['contentType'])->toContain('javascript');
    expect($assets->resolve('/assets/a.mjs')['contentType'])->toContain('javascript');
    expect($assets->resolve('/assets/a.woff')['kind'])->toBe('fingerprinted-asset');
    expect($assets->resolve('/assets/a.svg')['contentType'])->toContain('svg');
    expect($assets->resolve('/assets/a.png')['contentType'])->toContain('png');
    expect($assets->resolve('/assets/a.ico')['contentType'])->toContain('icon');
    expect($assets->resolve('/assets/a.json')['contentType'])->toContain('json');
    expect($assets->resolve('/assets/a.bin')['contentType'])->toContain('octet');
    expect($assets->resolve('/assets/finger-abcdef12-print.txt')['kind'])->toBe('fingerprinted-asset');
    rmdir_recursive($assetRoot);
});

function rmdir_recursive(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }
    foreach (scandir($dir) ?: [] as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir.'/'.$item;
        if (is_dir($path)) {
            rmdir_recursive($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}
