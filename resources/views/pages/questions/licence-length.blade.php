{!! \App\Govuk\Renderer::render('radios', [
  'name' => 'licenceLength',
  'fieldset' => ['legend' => ['text' => 'How long do you need a licence for?', 'isPageHeading' => true, 'classes' => 'govuk-fieldset__legend--l']],
  'errorMessage' => !empty($errors['licenceLength']) ? ['text' => $errors['licenceLength']] : null,
  'items' => [
    ['value' => '1-day', 'text' => '1 day', 'checked' => old('licenceLength', $app->licenceLength) === '1-day'],
    ['value' => '8-day', 'text' => '8 days', 'checked' => old('licenceLength', $app->licenceLength) === '8-day'],
    ['value' => '12-month', 'text' => '12 months', 'checked' => old('licenceLength', $app->licenceLength) === '12-month'],
  ],
]) !!}
