{{-- Show the inputs --}}
@foreach ($fields as $field)
    <!-- load the view from the application if it exists, otherwise load the one in the package -->
    @if(view()->exists('vendor.lara.crud.fields.'.$field['type']))
        @include('vendor.lara.crud.fields.'.$field['type'], array('field' => $field, 'fields' => $fields))
    @else
        <div class="col-md-12">
            @if(isset($field['type']) && is_string($field['type']))
                @include('crud.fields.'.$field['type'], array('field' => $field, 'fields' => $fields))
            @else
                {{--  @include('crud.fields.'.lcfirst($field['field_type']->name), array('field' => $field, 'fields' => $fields))  --}}
                @input($crud, $field->name)
            @endif
        </div>
    @endif
@endforeach