<!-- view field -->

<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
  @include($field['view'], compact('crud', 'entry', 'field'))
</div>
