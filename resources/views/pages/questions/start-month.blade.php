{!! \App\Govuk\Renderer::render('input', [
  'id' => 'startMonth',
  'name' => 'startMonth',
  'label' => ['text' => 'When should the licence start?', 'classes' => 'govuk-label--l', 'isPageHeading' => true],
  'hint' => ['text' => 'For example, March 2026'],
  'value' => old('startMonth', $app->startMonth),
  'errorMessage' => !empty($errors['startMonth']) ? ['text' => $errors['startMonth']] : null,
]) !!}
