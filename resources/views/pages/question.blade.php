@component('layouts.govuk', ['pageTitle' => $pageTitle, 'beforeContent' => $beforeContent ?? null])
  @php
    $errorList = [];
    foreach ($errors as $field => $message) {
      $errorList[] = ['href' => '#'.$field, 'text' => $message];
    }
  @endphp
  @if(count($errorList) > 0)
    {!! \App\Govuk\Renderer::render('error-summary', [
      'titleText' => 'There is a problem',
      'errorList' => $errorList,
    ]) !!}
  @endif
  <form method="post" action="{{ $step['path'] }}" novalidate>
    @csrf
    @include('pages.questions.'.$step['id'])
    {!! \App\Govuk\Renderer::render('button', ['text' => 'Continue']) !!}
  </form>
@endcomponent
