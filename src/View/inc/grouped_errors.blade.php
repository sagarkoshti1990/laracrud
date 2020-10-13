{{-- Show the errors, if any --}}
@if ($errors->any())
<div class="alert alert-danger alert-dismissable {{ $style_class }}">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="zmdi zmdi-block pr-15 float-left"></i><h4 class="float-left">{{ trans('crud.please_fix') }}</h4>
        <div class="clearfix"></div>
        <ol class="mt-20">
            @foreach($errors->all() as $error)
                <li class="ml-50">{{ $error }}</li>
            @endforeach
        </ol>
        <div class="clearfix"></div>
    </div>
@endif