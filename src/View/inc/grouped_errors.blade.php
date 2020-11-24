{{-- Show the errors, if any --}}
@if ($errors->any())
<div class="alert alert-danger alert-dismissable {{ $style_class }}" style="">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4 class="float-left h6">{{ trans('stlc.please_fix') }}</h4>
        <div class="clearfix"></div>
        <ol class="small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ol>
    </div>
@endif