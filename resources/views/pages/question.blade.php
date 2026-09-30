@component('layouts.govuk', ['pageTitle' => $pageTitle, 'beforeContent' => $beforeContent ?? null])
  @php
    $errorList = [];
    foreach ($errors as $field => $message) {
      $href = match ($field) {
        'licenceLength' => '#licenceLength',
        'fullName' => '#fullName',
        'date-of-birth' => '#date-of-birth-day',
        'country' => '#country',
        default => '#'.$field,
      };
      $errorList[] = ['href' => $href, 'text' => $message];
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
    @if(!empty($returnTo))
      <input type="hidden" name="returnTo" value="{{ $returnTo }}">
    @endif
    @include('pages.questions.'.$step['id'])
    {!! \App\Govuk\Renderer::render('button', ['text' => 'Continue']) !!}
  </form>
@endcomponent
