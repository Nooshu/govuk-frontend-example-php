{!! \App\Govuk\Renderer::render('input', [
  'id' => 'telephone',
  'name' => 'telephone',
  'type' => 'tel',
  'classes' => 'govuk-!-width-one-half',
  'label' => ['text' => 'Telephone number'],
  'value' => old('telephone', $app->telephone),
  'errorMessage' => !empty($errors['telephone']) ? ['text' => $errors['telephone']] : null,
]) !!}
