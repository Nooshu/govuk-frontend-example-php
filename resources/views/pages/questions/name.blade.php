<h1 class="govuk-heading-l">What is your name?</h1>
@if(!empty($errors['firstName']))
      {!! \App\Govuk\Renderer::render('error-message', ['id' => 'firstName-error', 'text' => $errors['firstName']]) !!}
    @endif
{!! \App\Govuk\Renderer::render('input', [
  'id' => 'firstName',
  'name' => 'firstName',
  'label' => ['text' => 'First name'],
  'value' => old('firstName', $app->firstName),
  'errorMessage' => !empty($errors['firstName']) ? ['text' => $errors['firstName']] : null,
]) !!}
@if(!empty($errors['lastName']))
      {!! \App\Govuk\Renderer::render('error-message', ['id' => 'lastName-error', 'text' => $errors['lastName']]) !!}
    @endif
{!! \App\Govuk\Renderer::render('input', [
  'id' => 'lastName',
  'name' => 'lastName',
  'label' => ['text' => 'Last name'],
  'value' => old('lastName', $app->lastName),
  'errorMessage' => !empty($errors['lastName']) ? ['text' => $errors['lastName']] : null,
]) !!}
