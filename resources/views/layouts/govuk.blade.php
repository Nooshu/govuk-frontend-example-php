<!DOCTYPE html>
<html lang="{{ $htmlLang ?? 'en' }}" class="govuk-template">
  <head>
    <meta charset="utf-8">
    <title>{{ $pageTitle ?? $serviceName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1d70b8">
    <link rel="stylesheet" href="{{ $stylesheetHref }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" sizes="48x48" href="/assets/images/favicon.ico">
    <link rel="icon" sizes="any" href="/assets/images/favicon.svg" type="image/svg+xml">
    <link rel="mask-icon" href="/assets/images/govuk-icon-mask.svg" color="#1d70b8">
    <link rel="apple-touch-icon" href="/assets/images/govuk-icon-180.png">
  </head>
  <body class="govuk-template__body">
    <script>{!! $jsEnabledSnippet !!}</script>
    {!! \App\Govuk\Renderer::render('skip-link', ['href' => '#main-content', 'text' => $skipLinkText ?? 'Skip to main content']) !!}
    @if(!empty($cookieBannerHtml))
      {!! $cookieBannerHtml !!}
    @endif
    <header class="govuk-template__header">
      {!! \App\Govuk\Renderer::render('header', ['homepageUrl' => $homepageUrl ?? '/']) !!}
      {!! \App\Govuk\Renderer::render('service-navigation', [
        'serviceName' => $serviceName,
        'serviceUrl' => '/',
        'navigation' => $serviceNavigation ?? [],
      ]) !!}
    </header>
    <div class="govuk-width-container">
      {!! \App\Govuk\Renderer::render('phase-banner', [
        'tag' => ['text' => $phaseTag ?? 'Example'],
        'html' => $phaseHtml ?? 'This is a demonstration – it is not a live government service. Your <a class="govuk-link" href="/help">feedback</a> will help us improve the example.',
      ]) !!}
      @isset($beforeContent)
        {!! $beforeContent !!}
      @endisset
      <main class="govuk-main-wrapper{{ isset($mainClasses) ? ' '.$mainClasses : '' }}" id="main-content">
        {!! \App\Govuk\Renderer::render('notification-banner', [
          'titleText' => 'Important',
          'text' => 'This is a live demo. It is not a real government service.',
          'classes' => 'app-demo-banner',
        ]) !!}
        {{ $slot }}
      </main>
    </div>
    <footer class="govuk-template__footer">
      {!! \App\Govuk\Renderer::render('footer', [
        'meta' => [
          'items' => [
            ['href' => '/help', 'text' => 'Help'],
            ['href' => '/fees', 'text' => 'Licence fees'],
            ['href' => '/updates', 'text' => 'Service updates'],
            ['href' => '/guidance', 'text' => 'Guidance'],
            ['href' => '/cookies', 'text' => 'Cookies'],
            ['href' => '/accessibility', 'text' => 'Accessibility'],
            ['href' => '/about', 'text' => 'About this example'],
            ['href' => '/components', 'text' => 'Component catalogue'],
            ['href' => '/examples/exit-this-page', 'text' => 'Example pages'],
          ],
        ],
      ]) !!}
    </footer>
    <script type="module" src="{{ $appModuleHref }}"></script>
  </body>
</html>
