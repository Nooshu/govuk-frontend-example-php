{!! \App\Govuk\Renderer::render('checkboxes', [
  'name' => 'regions',
  'fieldset' => ['legend' => ['text' => 'Where will you fish?', 'isPageHeading' => true, 'classes' => 'govuk-fieldset__legend--l']],
  'errorMessage' => !empty($errors['regions']) ? ['text' => $errors['regions']] : null,
  'items' => [
    ['value' => 'england', 'text' => 'England', 'checked' => in_array('england', old('regions', $app->regions), true)],
    ['value' => 'wales', 'text' => 'Wales', 'checked' => in_array('wales', old('regions', $app->regions), true)],
  ],
]) !!}
