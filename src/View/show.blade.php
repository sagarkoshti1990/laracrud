@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-widget widget-user mb0">
                <div class="bg-dark-purple p10 pb20 pr0">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="widget-user-username">{{ $item->$represent_attr }}</h3>
                        </div>
                        <div class="col-md-6 text-right pr30">
                            @include('crud.inc.button_stack', ['stack' => 'line', 'src' => $crud->route.'/'.$item->id, 'name' => ['update','delete'], 'crud' => $crud, 'entry' => $item])
                        </div>
                    </div>
                </div>
                <div class="widget-user-image">
                    <span class="info-box-icon" style="border-radius:100%;"><i class="fa {{ $crud->module->icon }}"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li><a href="{{ $src ?? url($crud->route) }}"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="active"><a href="#information" data-target="#tab-information" data-toggle="tab"><i class="fa fa-info-circle"></i>Information</a></li>
                    <li><a href="#logs" data-target="#tab-logs" data-toggle="tab"><i class="fa fa-history"></i>Logs</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab-information">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4>Information</h4>
                            </div>
                            <div class="box-body">
                                @displayAll($crud, [], ["class" => "col-md-6"])
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-logs">
                        @include('inc.activities.logs', ['crud' => $crud, 'item' => $item])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after_styles')
<style>
section.content{
    padding: 0;
}
</style>
@endpush

@push('after_scripts')
<script src="{{ asset('public/js/show_page.js') }}"></script>
@endpush