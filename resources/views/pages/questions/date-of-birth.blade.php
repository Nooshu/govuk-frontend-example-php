{!! \App\Govuk\Renderer::render('date-input', [
  'id' => 'date-of-birth',
  'namePrefix' => '',
  'fieldset' => [
    'legend' => [
      'text' => 'What is your date of birth?',
      'isPageHeading' => true,
      'classes' => 'govuk-fieldset__legend--l',
    ],
  ],
  'hint' => ['text' => 'For example, 31 3 1980'],
  'errorMessage' => !empty($errors['date-of-birth']) ? ['text' => $errors['date-of-birth']] : null,
  'items' => [
    ['name' => 'day', 'classes' => 'govuk-input--width-2', 'value' => old('day', $app->day)],
    ['name' => 'month', 'classes' => 'govuk-input--width-2', 'value' => old('month', $app->month)],
    ['name' => 'year', 'classes' => 'govuk-input--width-4', 'value' => old('year', $app->year)],
  ],
]) !!}
