<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Govuk\Renderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class StaticPageController extends Controller
{
    public function fees(): View
    {
        return view('pages.simple', [
            'pageTitle' => 'Licence fees – '.config('govuk.service_name'),
            'heading' => 'Licence fees',
            'bodyHtml' => Renderer::render('table', [
                'head' => [['text' => 'Licence'], ['text' => 'Cost']],
                'rows' => [
                    [['text' => '1 day'], ['text' => '£6']],
                    [['text' => '8 days'], ['text' => '£12']],
                    [['text' => '12 months'], ['text' => '£30']],
                ],
            ]),
            'beforeContent' => Renderer::render('back-link', ['href' => '/']),
        ]);
    }

    public function help(): View
    {
        return view('pages.simple', [
            'pageTitle' => 'Help – '.config('govuk.service_name'),
            'heading' => 'Help',
            'bodyHtml' => Renderer::render('accordion', [
                'id' => 'help',
                'items' => [
                    ['heading' => ['text' => 'Get help with this example'], 'content' => ['text' => 'This is a demonstration. It is not a live government service.']],
                    ['heading' => ['text' => 'Contact'], 'content' => ['html' => '<p class="govuk-body">Use the <a class="govuk-link" href="/about">about</a> page for more information.</p>']],
                ],
            ]),
            'beforeContent' => Renderer::render('back-link', ['href' => '/']),
        ]);
    }

    public function guidance(): View
    {
        return view('pages.simple', [
            'pageTitle' => 'Guidance – '.config('govuk.service_name'),
            'heading' => 'Guidance',
            'bodyHtml' => Renderer::render('tabs', [
                'items' => [
                    ['label' => 'Rules', 'id' => 'rules', 'panel' => ['text' => 'You must have a valid rod licence before you fish.']],
                    ['label' => 'Concessions', 'id' => 'concessions', 'panel' => ['text' => 'Concessions may apply for some applicants.']],
                ],
            ]),
            'beforeContent' => Renderer::render('back-link', ['href' => '/']),
        ]);
    }

    public function accessibility(): View
    {
        return view('pages.simple', [
            'pageTitle' => 'Accessibility statement – '.config('govuk.service_name'),
            'heading' => 'Accessibility statement',
            'bodyHtml' => '<p class="govuk-body">This example aims to meet WCAG 2.2 AA. It is a demonstration, not a live service.</p>',
            'beforeContent' => Renderer::render('back-link', ['href' => '/']),
        ]);
    }

    public function about(): View
    {
        return view('pages.simple', [
            'pageTitle' => 'About this example – '.config('govuk.service_name'),
            'heading' => 'About this example',
            'bodyHtml' => '<p class="govuk-body">This PHP Laravel example renders GOV.UK Frontend components with Blade and proves fixture parity for GOV.UK Frontend '.e((string) config('govuk.frontend_version')).'.</p>',
            'beforeContent' => Renderer::render('back-link', ['href' => '/']),
        ]);
    }

    public function updates(Request $request): View
    {
        $page = max(1, (int) $request->query('page', 1));

        return view('pages.simple', [
            'pageTitle' => 'Service updates – '.config('govuk.service_name'),
            'heading' => 'Service updates',
            'bodyHtml' => '<p class="govuk-body">Update notices for this example (page '.e((string) $page).').</p>'.
                Renderer::render('pagination', [
                    'previous' => $page > 1 ? ['href' => '/updates?page='.($page - 1)] : null,
                    'next' => ['href' => '/updates?page='.($page + 1)],
                    'items' => [
                        ['number' => 1, 'href' => '/updates?page=1', 'current' => $page === 1],
                        ['number' => 2, 'href' => '/updates?page=2', 'current' => $page === 2],
                    ],
                ]),
            'beforeContent' => Renderer::render('back-link', ['href' => '/']),
        ]);
    }

    public function cookies(): View
    {
        return view('pages.simple', [
            'pageTitle' => 'Cookies – '.config('govuk.service_name'),
            'heading' => 'Cookies',
            'bodyHtml' => view('pages.partials.cookies-form')->render(),
            'beforeContent' => Renderer::render('back-link', ['href' => '/']),
        ]);
    }

    public function saveCookies(Request $request): RedirectResponse
    {
        $choice = $request->input('cookies');
        if (in_array($choice, ['yes', 'no'], true)) {
            $request->session()->put('cookie_choice', $choice);
        }

        return redirect('/cookies')->with('success', true);
    }

    public function cookieChoices(Request $request): RedirectResponse
    {
        $choice = $request->input('cookies');
        if (in_array($choice, ['accept', 'reject'], true)) {
            $request->session()->put('cookie_choice', $choice === 'accept' ? 'yes' : 'no');
        }
        $return = (string) $request->input('returnPath', '/');

        return redirect($return !== '' ? $return : '/');
    }

    public function exitThisPage(): View
    {
        return view('pages.simple', [
            'pageTitle' => 'Exit this page example – '.config('govuk.service_name'),
            'heading' => 'Exit this page',
            'bodyHtml' => Renderer::render('exit-this-page', []).
                Renderer::render('warning-text', [
                    'text' => 'Use this component only on services where someone may be in danger.',
                    'iconFallbackText' => 'Warning',
                ]).
                Renderer::render('inset-text', [
                    'text' => 'This page is an example of the component. It is not part of the rod licence application. Choosing the button leaves this example and opens the BBC weather forecast.',
                ]).
                '<p class="govuk-body">The button is the first thing on the page, which matches the component guidance.</p>',
            'beforeContent' => Renderer::render('back-link', ['text' => 'Back', 'href' => '/examples']),
        ]);
    }

    public function examples(): View
    {
        return view('pages.simple', [
            'pageTitle' => 'Example pages – '.config('govuk.service_name'),
            'heading' => 'Example pages',
            'bodyHtml' => '<p class="govuk-body">These pages show GOV.UK patterns that are not steps in the rod licence application.</p>'.
                '<ul class="govuk-list">'.
                '<li><a class="govuk-link" href="/examples/exit-this-page">Exit this page</a></li>'.
                '</ul>',
            'beforeContent' => Renderer::render('breadcrumbs', [
                'items' => [
                    ['href' => '/', 'text' => 'Home'],
                    ['text' => 'Example pages'],
                ],
            ]),
        ]);
    }
}
