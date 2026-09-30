@component('layouts.govuk', ['pageTitle' => $pageTitle])
  {!! \App\Govuk\Renderer::render('panel', [
    'titleText' => 'Application complete',
    'html' => 'Your reference number<br><strong>'.e($app->reference).'</strong>',
  ]) !!}
  <p class="govuk-body">We have sent a confirmation email to {{ $app->email }}.</p>
  <p class="govuk-body"><a class="govuk-link" href="/">Return to the start</a></p>
@endcomponent
