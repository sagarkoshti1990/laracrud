@php
	if(!isset($class)) {
		if(isset($field['attributes'],$field['attributes']['class']) && !empty($field['attributes']['class'])) {
			$class =  $field['attributes']['class'].' f-form-control';
		} else {
			$class =  config("stlc.css.form_control","form-control").' f-form-control';
		}
	}
	$field['attributes']['class'] = (isset($errors) && $errors->has($field['name'])) ? $class.' is-invalid' : $class;
@endphp
@foreach ($field['attributes'] as $attribute => $value)
	@if (is_string($attribute)) {{ $attribute }}="{{ $value }}"@endif
@endforeach