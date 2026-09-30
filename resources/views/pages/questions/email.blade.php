{!! \App\Govuk\Renderer::render('input', [
  'id' => 'email',
  'name' => 'email',
  'type' => 'email',
  'autocomplete' => 'email',
  'label' => ['text' => 'What is your email address?', 'classes' => 'govuk-label--l', 'isPageHeading' => true],
  'value' => old('email', $app->email),
  'errorMessage' => !empty($errors['email']) ? ['text' => $errors['email']] : null,
]) !!}
