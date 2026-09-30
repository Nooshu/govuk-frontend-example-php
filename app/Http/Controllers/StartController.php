<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Govuk\Renderer;
use Illuminate\View\View;

final class StartController extends Controller
{
    public function english(): View
    {
        return $this->start('en');
    }

    public function welsh(): View
    {
        return $this->start('cy');
    }

    private function start(string $lang): View
    {
        $serviceName = (string) config('govuk.service_name');

        return view('pages.start', [
            'htmlLang' => $lang,
            'pageTitle' => $serviceName,
            'serviceName' => $serviceName,
            'lang' => $lang,
            'demosEnabled' => (bool) config('govuk.demos_enabled'),
            'languageNav' => Renderer::render('language-navigation', [
                'items' => [
                    ['text' => 'English', 'href' => '/', 'lang' => 'en', 'active' => $lang === 'en'],
                    ['text' => 'Cymraeg', 'href' => '/cy', 'lang' => 'cy', 'active' => $lang === 'cy'],
                ],
            ]),
        ]);
    }
}
