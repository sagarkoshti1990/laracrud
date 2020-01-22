@if (isset($field['wrapperAttributes']))
    @foreach ($field['wrapperAttributes'] as $attribute => $value)
    	@if (is_string($attribute))
        {{ $attribute }}="{{ $value }}"
        @endif
    @endforeach

    @if (!isset($field['wrapperAttributes']['class']))
		class="form-group @if(isset($field_name)){{ $errors->has($field_name) ? ' has-error' : '' }}@endif"
    @endif
@else
	class="form-group @if(isset($field_name)){{ $errors->has($field_name) ? ' has-error' : '' }}@endif"
@endif