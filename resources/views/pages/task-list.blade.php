@component('layouts.govuk', ['pageTitle' => $pageTitle, 'beforeContent' => $beforeContent ?? null])
  <h1 class="govuk-heading-l">Apply for a rod fishing licence</h1>
  <p class="govuk-body">Complete each section. You can return to a section to change your answers.</p>
  {!! $taskListHtml !!}
  @if($canCheck)
    <p class="govuk-body">
      <a class="govuk-link" href="/check-answers">Check your answers</a>
    </p>
  @endif
@endcomponent
