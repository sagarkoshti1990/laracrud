@php
  $field['wrapperAttributes']['class'] = isset($field['wrapperAttributes'],$field['wrapperAttributes']['class']) ? $field['wrapperAttributes']['class'].' f-form-group' : config("stlc.css.form_group","form-group").' f-form-group';
@endphp
@foreach ($field['wrapperAttributes'] as $attribute => $value)
  @if (is_string($attribute)) {{ $attribute }}="{{ $value }}" @endif
@endforeach