@if(session('success'))
  {!! \App\Govuk\Renderer::render('notification-banner', ['type' => 'success', 'titleText' => 'Success', 'text' => 'Your cookie settings were saved']) !!}
@endif
<form method="post" action="/cookies" novalidate>
  @csrf
  {!! \App\Govuk\Renderer::render('radios', [
    'name' => 'cookies',
    'fieldset' => ['legend' => ['text' => 'Do you want to accept analytics cookies?']],
    'items' => [
      ['value' => 'yes', 'text' => 'Yes'],
      ['value' => 'no', 'text' => 'No'],
    ],
  ]) !!}
  {!! \App\Govuk\Renderer::render('button', ['text' => 'Save cookie settings']) !!}
</form>
