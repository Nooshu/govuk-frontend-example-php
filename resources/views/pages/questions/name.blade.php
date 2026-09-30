{!! \App\Govuk\Renderer::render('input', [
  'id' => 'fullName',
  'name' => 'fullName',
  'autocomplete' => 'name',
  'label' => [
    'text' => 'What is your full name?',
    'classes' => 'govuk-label--l',
    'isPageHeading' => true,
  ],
  'value' => old('fullName', $app->fullName),
  'errorMessage' => !empty($errors['fullName']) ? ['text' => $errors['fullName']] : null,
]) !!}
