<!-- used for heading, separators, etc -->
<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
	{!! $field['value'] !!}
</div>