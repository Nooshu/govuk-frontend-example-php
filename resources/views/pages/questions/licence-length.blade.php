{!! \App\Govuk\Renderer::render('radios', [
  'idPrefix' => 'licenceLength',
  'name' => 'licenceLength',
  'fieldset' => [
    'legend' => [
      'text' => 'How long do you need the licence for?',
      'isPageHeading' => true,
      'classes' => 'govuk-fieldset__legend--l',
    ],
  ],
  'errorMessage' => !empty($errors['licenceLength']) ? ['text' => $errors['licenceLength']] : null,
  'items' => [
    ['value' => '1-day', 'text' => '1 day', 'checked' => old('licenceLength', $app->licenceLength) === '1-day'],
    ['value' => '8-days', 'text' => '8 days', 'checked' => old('licenceLength', $app->licenceLength) === '8-days'],
    ['value' => '12-months', 'text' => '12 months', 'checked' => old('licenceLength', $app->licenceLength) === '12-months'],
  ],
]) !!}
