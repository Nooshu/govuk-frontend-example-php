{!! \App\Govuk\Renderer::render('password-input', [
  'id' => 'password',
  'name' => 'password',
  'label' => ['text' => 'Create a password', 'classes' => 'govuk-label--l', 'isPageHeading' => true],
  'errorMessage' => !empty($errors['password']) ? ['text' => $errors['password']] : null,
]) !!}
{!! \App\Govuk\Renderer::render('password-input', [
  'id' => 'confirmPassword',
  'name' => 'confirmPassword',
  'label' => ['text' => 'Confirm password'],
  'errorMessage' => !empty($errors['confirmPassword']) ? ['text' => $errors['confirmPassword']] : null,
]) !!}
