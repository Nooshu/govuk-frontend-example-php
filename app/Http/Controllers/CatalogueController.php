<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Govuk\Catalogue;
use App\Govuk\FixtureLoader;
use App\Govuk\Parity;
use App\Govuk\Renderer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class CatalogueController extends Controller
{
    public function index(): View
    {
        $loader = FixtureLoader::fromConfig();
        $entries = [];
        foreach ($loader->componentNames() as $name) {
            $entries[] = Catalogue::describe($name);
        }

        return view('pages.components', [
            'pageTitle' => 'Component catalogue – '.config('govuk.service_name'),
            'entries' => $entries,
            'beforeContent' => Renderer::render('breadcrumbs', [
                'items' => [
                    ['text' => 'Home', 'href' => '/'],
                    ['text' => 'Component catalogue'],
                ],
            ]),
        ]);
    }

    public function show(Request $request, string $name): View
    {
        $loader = FixtureLoader::fromConfig();
        if (! in_array($name, $loader->componentNames(), true)) {
            abort(404);
        }
        $set = $loader->load($name);
        $fixtureName = (string) $request->query('fixture', $set['fixtures'][0]['name'] ?? 'default');
        $selected = null;
        foreach ($set['fixtures'] as $fixture) {
            if ($fixture['name'] === $fixtureName) {
                $selected = $fixture;
                break;
            }
        }
        if ($selected === null) {
            abort(404);
        }

        $rendered = Renderer::render($name, $selected['options']);
        $matches = Parity::matches($rendered, $selected['html']);
        $banner = Parity::banner($matches);
        $info = Catalogue::describe($name);

        $versions = [];
        foreach ($set['fixtures'] as $fixture) {
            $versions[] = [
                'name' => $fixture['name'],
                'current' => $fixture['name'] === $selected['name'],
            ];
        }

        $parityHtml = Renderer::render('notification-banner', array_filter([
            'type' => $banner['type'] !== '' ? $banner['type'] : null,
            'titleText' => $banner['titleText'],
            'text' => $banner['text'],
        ], fn ($v) => $v !== null));

        return view('pages.component', [
            'pageTitle' => $info['title'].' – '.config('govuk.service_name'),
            'info' => $info,
            'fixtureName' => $selected['name'],
            'fixtureDescription' => $selected['description'],
            'rendered' => $rendered,
            'parityHtml' => $parityHtml,
            'matches' => $matches,
            'versions' => $versions,
            'frontendVersion' => config('govuk.frontend_version'),
            'beforeContent' => Renderer::render('back-link', [
                'href' => '/components',
                'text' => 'Back',
            ]),
        ]);
    }

    public function fixture(Request $request, string $name): Response
    {
        $loader = FixtureLoader::fromConfig();
        $set = $loader->load($name);
        $fixtureName = (string) $request->query('fixture', $set['fixtures'][0]['name'] ?? 'default');
        foreach ($set['fixtures'] as $fixture) {
            if ($fixture['name'] === $fixtureName) {
                $html = Renderer::render($name, $fixture['options']);

                return response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
            }
        }
        abort(404);
    }
}
