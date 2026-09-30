@component('layouts.govuk', ['pageTitle' => $pageTitle])
  {!! \App\Govuk\Renderer::render('panel', [
    'titleText' => 'Application complete',
    'html' => 'Your example reference number<br><strong>'.e($app->reference).'</strong>',
  ]) !!}
  <p class="govuk-body">This is a fictional example. Nobody will send you a fishing rod licence.</p>
  <p class="govuk-body"><a class="govuk-link" href="/components">Back to the component list</a></p>
@endcomponent
