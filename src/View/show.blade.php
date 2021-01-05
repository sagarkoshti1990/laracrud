@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div @attributes($crud,'show.card_widget',['class'=>"card card-widget widget-user mb-0 ".config('stlc.show_bg'), 'style'=>"min-height: 70px"])>
                <div @attributes($crud,'show.bg_color',["class"=>"bg-dark-purple p-3"])>
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="widget-user-username">{{ $represent_value ?? '' }}</h3>
                        </div>
                        <div class="col-md-6 text-right pr30">
                            @include(config('stlc.view_path.inc.button_stack','stlc::inc.button_stack'), ['name' => ['clone','edit','delete'], 'src' => $crud->route.'/'.$item->id, 'crud' => $crud,'from_view' => 'show'])
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
            <div @attributes($crud,'show.nav_tabs_custom',["class"=>"nav-tabs-custom"])>
                <div @attributes($crud,'show.nav_tabs',["class"=>"nav nav-tabs","id"=>"nav-tab","role"=>"tablist"])>
                    <a @attributes($crud,'show.nav_tabs',["class"=>"nav-item nav-link"]) href="{{ $src ?? url($crud->route) }}"><i class="{{ config('stlc.view.icon.show_arrow_left','fa fa-arrow-left') }}"></i></a>
                    <a @attributes($crud,'show.nav_tabs_active',["class"=>"nav-item nav-link active","data-toggle"=>"tab"]) href="#information" data-target="#tab-information"><i class="{{ config('stlc.view.icon.show_info','fa fa-info-circle mr-2') }}"></i>{{ $crud->label }} {{ trans('stlc.details') }}</a>
                    <a @attributes($crud,'show.nav_tabs',["class"=>"nav-item nav-link","data-toggle"=>"tab"]) href="#logs" data-target="#tab-logs"><i class="{{ config('stlc.view.icon.show_log','fa fa-history mr-2') }}"></i>{{ trans('stlc.logs') }}</a>
                    @if(\Module::user()->isAdmin() && in_array($crud->name ,config('stlc.access_show',[])))
                        <a @attributes($crud,'show.nav_tabs',["class"=>"nav-item nav-link","data-toggle"=>"tab"]) href="#access" data-target="#tab-access"><i class="{{ config('stlc.view.icon.show_access','fa fa-lock mr-2') }}"></i>{{ trans('stlc.access') }}</a>
                    @endif
                </div>
                <div @attributes($crud,'show.tab_content',["class"=>"tab-content"])>
                    <div @attributes($crud,'show.tab_pane_active',['class'=>"tab-pane fade show in active"]) id="tab-information">
                        <div @attributes($crud,'show.card',['class'=>"card"])>
                            <div @attributes($crud,'show.card_header',['class'=>"card-header with-border d-none"])>
                                <h4>Information</h4>
                            </div>
                            <div @attributes($crud,'show.card_body',['class'=>"card-body list-group-flush"])>
                                @displayAll($crud, [], ["class" => "col-md-6"])
                            </div>
                        </div>
                    </div>
                    <div @attributes($crud,'show.tab_pane',['class'=>"tab-pane fade"]) id="tab-logs">
                        @include(config('stlc.view_path.inc.activities.logs','stlc::inc.activities.logs'), ['crud' => $crud, 'item' => $item])
                    </div>
                    @if(\Module::user()->isAdmin() && in_array($crud->name ,config('stlc.access_show',[])))
                        <div @attributes($crud,'show.tab_pane',['class'=>"tab-pane fade"]) id="tab-access">
                            @include(config('stlc.view_path.inc.access','stlc::inc.access'), ['crud' => $crud, 'item' => $item])
                        </div>
                    @endif
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