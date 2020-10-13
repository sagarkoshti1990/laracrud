
<button @if(isset($submit_type)) type="{{ $submit_type }}" @else type="submit" @endif  class="btn bg-orange btn-flat float-right @if(isset($submit_class)){{$submit_class}}@endif">
   
    {{ $button_submit_name ?? 'save' }}
</button>
@if(!isset($button_cancel) || $button_cancel)
<a
    @if(isset($model_close))
        data-toggle="modal" data-target="#{{$model_close}}"
    @else
        href="@if(isset($src)){{ url($src) }}@elseif(isset($crud)){{ url($crud->route) }}@endif"
    @endif
        class="btn btn-default btn-flat float-left @if(isset($cancel_class)){{$cancel_class}}@endif">
    
    {{ $button_cancel_name ?? 'cancel' }}
</a>
@endif