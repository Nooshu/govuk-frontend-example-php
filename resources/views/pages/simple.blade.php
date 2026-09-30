@component('layouts.govuk', ['pageTitle' => $pageTitle, 'beforeContent' => $beforeContent ?? null])
  <h1 class="govuk-heading-l">{{ $heading }}</h1>
  {!! $bodyHtml !!}
@endcomponent
