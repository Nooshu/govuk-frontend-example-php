@component('layouts.govuk', ['pageTitle' => $pageTitle, 'beforeContent' => $beforeContent ?? null])
  <h1 class="govuk-heading-l">Check your answers</h1>
  {!! \App\Govuk\Renderer::render('summary-list', [
    'rows' => $rows,
  ]) !!}
  <form method="post" action="/check-answers" novalidate>
    @csrf
    {!! \App\Govuk\Renderer::render('button', ['text' => 'Accept and continue']) !!}
  </form>
@endcomponent
