{!! \App\Govuk\Renderer::render('file-upload', [
  'id' => 'evidence',
  'name' => 'evidence',
  'label' => ['text' => 'Upload evidence of a concession', 'classes' => 'govuk-label--l', 'isPageHeading' => true],
  'hint' => ['text' => 'You can upload a PDF, PNG or JPG. This step is optional.'],
]) !!}
@if($app->evidenceFilename !== '')
  <p class="govuk-body">Current file: {{ $app->evidenceFilename }}</p>
@endif
