{!! \App\Govuk\Renderer::render('input', [
  'id' => 'email',
  'name' => 'email',
  'type' => 'email',
  'autocomplete' => 'email',
  'spellcheck' => false,
  'label' => [
    'text' => 'What is your email address?',
    'classes' => 'govuk-label--l',
    'isPageHeading' => true,
  ],
  'hint' => ['text' => 'This example stores the address in your browser session only.'],
  'value' => old('email', $app->email),
  'errorMessage' => !empty($errors['email']) ? ['text' => $errors['email']] : null,
]) !!}
