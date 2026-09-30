{!! \App\Govuk\Renderer::render('radios', [
  'name' => 'contactBy',
  'fieldset' => ['legend' => ['text' => 'How should we contact you?', 'isPageHeading' => true, 'classes' => 'govuk-fieldset__legend--l']],
  'errorMessage' => !empty($errors['contactBy']) ? ['text' => $errors['contactBy']] : null,
  'items' => [
    ['value' => 'email', 'text' => 'Email', 'checked' => old('contactBy', $app->contactBy) === 'email'],
    ['value' => 'telephone', 'text' => 'Telephone', 'checked' => old('contactBy', $app->contactBy) === 'telephone',
     'conditional' => ['html' => view('pages.partials.telephone', compact('app','errors'))->render()]],
  ],
]) !!}
