@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-widget widget-user mb-0 {{ config('stlc.show_bg') }}" style="min-height: 70px">
                <div class="bg-dark-purple p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="widget-user-username">{{ $item->$represent_attr }}</h3>
                        </div>
                        <div class="col-md-6 text-right pr30">
                            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.button_stack', ['stack' => 'line', 'src' => $crud->route.'/'.$item->id, 'name' => ['update','delete'], 'crud' => $crud])
                        </div>
                    </div>
                </div>
                <div class="widget-user-image" style="top: 5px;">
                    <span class="img-circle elevation-2 py-1 px-2" style="font-size:30px;line-height:60px;"><i class="{{ $crud->module->icon }}"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link" href="{{ $src ?? url($crud->route) }}"><i class="fa fa-arrow-left"></i></a>
                    <a class="nav-item nav-link active" href="#information" data-target="#tab-information" data-toggle="tab"><i class="fa fa-info-circle mr-2"></i>{{ $crud->label }} {{ trans('stlc.details') }}</a>
                    <a class="nav-item nav-link" href="#logs" data-target="#tab-logs" data-toggle="tab"><i class="fa fa-history mr-2"></i>{{ trans('stlc.logs') }}</a>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show in active" id="tab-information">
                        <div class="card">
                            {{-- <div class="card-header with-border">
                                <h4>Information</h4>
                            </div> --}}
                            <div class="card-body list-group-flush">
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