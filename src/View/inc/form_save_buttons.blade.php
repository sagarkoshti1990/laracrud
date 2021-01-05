
@php
	$from_view = $from_view ?? 'index';
@endphp
@if(!isset($button_cancel) || $button_cancel)
<a
    @if(isset($model_close))
        data-toggle="modal" data-target="#{{$model_close}}"
    @else
        href="@if(isset($src)){{ url($src) }}@elseif(isset($crud)){{ url($crud->route) }}@endif"
    @endif
        @if(isset($cancel_class)) type="{{ $cancel_class }}" @endif
        @attributes($crud,$from_view.'.button.cancel',["class"=>'btn btn-secondary btn-flat float-left'])
    >
    
    {{ $button_cancel_name ?? 'Cancel' }}
</a>
@endif
<button @if(isset($submit_type)) type="{{ $submit_type }}" @endif 
    @if(isset($submit_class)) type="{{ $submit_class }}" @endif
    @attributes($crud,$from_view.'.button.save',["class"=>'btn btn-primary btn-flat float-right','type'=>"submit"])
    >
    {{ $button_submit_name ?? 'Save' }}
</button>