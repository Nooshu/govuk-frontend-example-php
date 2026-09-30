@component('layouts.govuk', ['pageTitle' => $pageTitle, 'beforeContent' => $beforeContent ?? null])
  <h1 class="govuk-heading-l">Check your answers</h1>
  {!! \App\Govuk\Renderer::render('summary-list', [
    'rows' => [
      ['key' => ['text' => 'Name'], 'value' => ['text' => $app->firstName.' '.$app->lastName], 'actions' => ['items' => [['href' => '/name', 'text' => 'Change', 'visuallyHiddenText' => 'name']]]],
      ['key' => ['text' => 'Date of birth'], 'value' => ['text' => $app->day.'/'.$app->month.'/'.$app->year], 'actions' => ['items' => [['href' => '/date-of-birth', 'text' => 'Change', 'visuallyHiddenText' => 'date of birth']]]],
      ['key' => ['text' => 'Email'], 'value' => ['text' => $app->email], 'actions' => ['items' => [['href' => '/email', 'text' => 'Change', 'visuallyHiddenText' => 'email']]]],
      ['key' => ['text' => 'Contact preference'], 'value' => ['text' => $app->contactBy], 'actions' => ['items' => [['href' => '/contact-preference', 'text' => 'Change', 'visuallyHiddenText' => 'contact preference']]]],
      ['key' => ['text' => 'Where you will fish'], 'value' => ['text' => implode(', ', $app->regions)], 'actions' => ['items' => [['href' => '/where-you-will-fish', 'text' => 'Change', 'visuallyHiddenText' => 'where you will fish']]]],
      ['key' => ['text' => 'Licence length'], 'value' => ['text' => $app->licenceLength], 'actions' => ['items' => [['href' => '/licence-length', 'text' => 'Change', 'visuallyHiddenText' => 'licence length']]]],
      ['key' => ['text' => 'Start month'], 'value' => ['text' => $app->startMonth], 'actions' => ['items' => [['href' => '/start-month', 'text' => 'Change', 'visuallyHiddenText' => 'start month']]]],
      ['key' => ['text' => 'Address'], 'value' => ['html' => e($app->addressLine1).'<br>'.e($app->addressLine2).($app->addressLine2 !== '' ? '<br>' : '').e($app->town).'<br>'.e($app->postcode)], 'actions' => ['items' => [['href' => '/address', 'text' => 'Change', 'visuallyHiddenText' => 'address']]]],
    ],
  ]) !!}
  <form method="post" action="/check-answers" novalidate>
    @csrf
    {!! \App\Govuk\Renderer::render('button', ['text' => 'Accept and send']) !!}
  </form>
@endcomponent
