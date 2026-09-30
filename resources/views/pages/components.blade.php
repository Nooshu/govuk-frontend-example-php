@component('layouts.govuk', ['pageTitle' => $pageTitle, 'beforeContent' => $beforeContent ?? null])
  <h1 class="govuk-heading-xl">Component catalogue</h1>
  <p class="govuk-body">
    Preview GOV.UK Frontend {{ config('govuk.frontend_version') }} components.
    Markup is rendered by this service’s PHP component library. Each link opens that component’s page with fixtures and a live preview.
  </p>
  <ul class="govuk-list">
    @foreach($entries as $entry)
      <li>
        <h2 class="govuk-heading-s govuk-!-margin-bottom-1">
          <a class="govuk-link" href="/components/{{ $entry['name'] }}">{{ $entry['title'] }}</a>
        </h2>
        <p class="govuk-body">{{ $entry['description'] }}</p>
      </li>
    @endforeach
  </ul>
@endcomponent
