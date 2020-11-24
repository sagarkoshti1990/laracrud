
@if (isset($field['wrapperAttributes']))
    @php
      $field['wrapperAttributes']['class'] = $errors->has($field['name']) ? ($field['wrapperAttributes']['class'] ?? "").' is-invalid' : (isset($field['wrapperAttributes']['class']) ? $field['wrapperAttributes']['class'] : "form-group");
    @endphp
    @foreach ($field['wrapperAttributes'] as $attribute => $value)
      @if (is_string($attribute)) {{ $attribute }}="{{ $value }}" @endif
    @endforeach
    @if (!isset($field['wrapperAttributes']['class'])) @if($errors->has($field['name'])) class="form-group is-invalid" @else class="form-group" @endif @endif
@else
  @if($errors->has($field['name'])) class="form-group is-invalid" @else class="form-group" @endif
@endif