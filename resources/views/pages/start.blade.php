@component('layouts.govuk', ['pageTitle' => $pageTitle, 'htmlLang' => $htmlLang ?? 'en'])
  @if(!empty($languageNav))
    {!! $languageNav !!}
  @endif
  {!! \App\Govuk\Renderer::render('notification-banner', [
    'titleText' => 'Important',
    'text' => 'The 2026 to 2027 rod licence is now available.',
  ]) !!}
  <h1 class="govuk-heading-xl">{{ config('govuk.service_name') }}</h1>
  <p class="govuk-body-l">Use this service to apply for a licence to fish with a rod.</p>
  {!! \App\Govuk\Renderer::render('button', [
    'text' => 'Start now',
    'href' => '/task-list',
    'isStartButton' => true,
  ]) !!}
  <p class="govuk-body">Applying takes about 10 minutes.</p>
  {!! \App\Govuk\Renderer::render('warning-text', [
    'text' => 'You must have a valid rod licence before you fish.',
  ]) !!}
  {!! \App\Govuk\Renderer::render('inset-text', [
    'text' => 'You need to be 13 or over to apply. Someone aged 13 to 16 can apply with help from a parent or guardian.',
  ]) !!}
  {!! \App\Govuk\Renderer::render('details', [
    'summaryText' => 'What you will need',
    'text' => 'Your name, date of birth, email address, and where you will fish.',
  ]) !!}
  @if($demosEnabled ?? false)
    <h2 class="govuk-heading-m">Developer previews</h2>
    <p class="govuk-body">
      <a class="govuk-link" href="/components">Preview GOV.UK components</a>
      — a separate page for each component, rendered by this service’s PHP library.
    </p>
  @endif
@endcomponent
