@if ($crud->hasAccess('deactivate'))
    <a href="{{ url($crud->route.'/'.$entry->getKey().'/restore') }}" class="btn @if(isset($class_btn)){{ $class_btn }}@else btn-xs mt5 @endif btn-purple" data-button-type="restore" data-toggle="tooltip" title="Active"><i class="fa fa-history"></i></a>
@endif