@extends(config('stlc.stlc_modules_folder_name','stlc::').'layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-widget widget-user mb-0 bg-info">
                <div class="bg-dark-purple p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="widget-user-username">{{ $item->$represent_attr }}</h3>
                        </div>
                        <div class="col-md-6 text-right pr30">
                            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.button_stack', ['stack' => 'line', 'src' => $crud->route.'/'.$item->id, 'name' => ['update','delete'], 'crud' => $crud, 'entry' => $item])
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
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link" href="{{ $src ?? url($crud->route) }}"><i class="fa fa-arrow-left"></i></a>
                    <a class="nav-item nav-link active" href="#information" data-target="#tab-information" data-toggle="tab"><i class="fa fa-info-circle"></i>{{ $crud->label }} Details</a>
                    <a class="nav-item nav-link" href="#logs" data-target="#tab-logs" data-toggle="tab"><i class="fa fa-history"></i>Logs</a>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show in active" id="tab-information">
                        <div class="box">
                            {{-- <div class="box-header with-border">
                                <h4>Information</h4>
                            </div> --}}
                            <div class="box-body">
                                @displayAll($crud, [], ["class" => "col-md-6"])
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-logs">
                        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.activities.logs', ['crud' => $crud, 'item' => $item])
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