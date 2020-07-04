<!-- hidden input -->
{{--  <div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >  --}}
  <input
  	type="hidden"
    name="{{ $field['name'] }}"
    value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
    @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')
  	>
{{--  </div>  --}}