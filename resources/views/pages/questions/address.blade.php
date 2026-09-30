<h1 class="govuk-heading-l">What is your address?</h1>
{!! \App\Govuk\Renderer::render('input', ['id'=>'addressLine1','name'=>'addressLine1','label'=>['text'=>'Address line 1'],'value'=>old('addressLine1',$app->addressLine1),'errorMessage'=>!empty($errors['addressLine1'])?['text'=>$errors['addressLine1']]:null]) !!}
{!! \App\Govuk\Renderer::render('input', ['id'=>'addressLine2','name'=>'addressLine2','label'=>['text'=>'Address line 2 (optional)'],'value'=>old('addressLine2',$app->addressLine2)]) !!}
{!! \App\Govuk\Renderer::render('input', ['id'=>'town','name'=>'town','label'=>['text'=>'Town or city'],'value'=>old('town',$app->town),'errorMessage'=>!empty($errors['town'])?['text'=>$errors['town']]:null]) !!}
{!! \App\Govuk\Renderer::render('input', ['id'=>'postcode','name'=>'postcode','classes'=>'govuk-input--width-10','label'=>['text'=>'Postcode'],'value'=>old('postcode',$app->postcode),'errorMessage'=>!empty($errors['postcode'])?['text'=>$errors['postcode']]:null]) !!}
