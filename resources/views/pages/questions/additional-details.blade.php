{!! \App\Govuk\Renderer::render('character-count', [
  'id' => 'additionalDetails',
  'name' => 'additionalDetails',
  'label' => ['text' => 'Is there anything else we should know?', 'classes' => 'govuk-label--l', 'isPageHeading' => true],
  'maxlength' => 200,
  'value' => old('additionalDetails', $app->additionalDetails),
]) !!}
