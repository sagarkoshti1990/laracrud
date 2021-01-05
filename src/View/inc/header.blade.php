@php
    $icon = $icon ?? $crud->icon ?? '';
    $headeing = $headeing ?? $crud->labelPlural ?? "";
    $sub_headeing = $sub_headeing ?? null;
    $title_link = $title_link ?? false;
@endphp
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    @if($title_link == true)<a href="{{ url($crud->route) }}">@endif
                        <span class="{{ $icon }}"></span>
                        <span class="text-capitalize">{{ $headeing }}</span>
                    @if($title_link == true)</a>@endif
                    @if(isset($sub_headeing))
                        <small>{{ $sub_headeing }}</small>
                    @endif
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url(config('stlc.route_prefix'), 'dashboard') }}">{{ trans('stlc.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $headeing }}</a></li>
                    @if(isset($sub_headeing))<li class="breadcrumb-item active">{{ $sub_headeing }}</li>@endif
                </ol>
            </div>
        </div>
    </div>
</div>