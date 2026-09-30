{!! \App\Govuk\Renderer::render('radios', [
  'idPrefix' => 'country',
  'name' => 'country',
  'fieldset' => [
    'legend' => [
      'text' => 'Where will you fish?',
      'isPageHeading' => true,
      'classes' => 'govuk-fieldset__legend--l',
    ],
  ],
  'hint' => ['text' => 'This example is fictional. It does not check a real fishing area.'],
  'errorMessage' => !empty($errors['country']) ? ['text' => $errors['country']] : null,
  'items' => [
    ['value' => 'England', 'text' => 'England', 'checked' => old('country', $app->country) === 'England'],
    ['value' => 'Wales', 'text' => 'Wales', 'checked' => old('country', $app->country) === 'Wales'],
    ['value' => 'Scotland', 'text' => 'Scotland', 'checked' => old('country', $app->country) === 'Scotland'],
  ],
]) !!}
