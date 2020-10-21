@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-widget widget-user mb-0 bg-info">
                <div class="bg-dark-purple p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="widget-user-username fonttxt">{{ $field->$represent_attr }}</h3>
                            {{--  <h5 class="widget-user-desc">Founder &amp; CEO</h5>  --}}
                        </div>
                        <div class="col-md-6 text-right">
                            @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.button_stack', ['stack' => 'line', 'src' => $crud->route.'/'.$field->id, 'name' => ['update','delete'], 'crud' => $crud, 'entry' => $field])
                        </div>
                    </div>
                </div>
                <div class="widget-user-image" style="top: 5px;">
                    <span class="info-box-icon" style="border-radius:100%;height: 55px;width: 55px;font-size: 30px;line-height: 60px;"><i class="{{ $crud->module->icon }}"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link" href="{{ $src ?? url($crud->route) }}"><i class="fa fa-arrow-left"></i></a>
                    <a class="nav-item nav-link" href="#information" data-target="#tab-information" data-toggle="tab"><i class="fa fa-info-circle mr-2"></i>Information</a>
                    {{-- <a class="nav-item nav-link" href="#timeline" data-target="#tab-timeline" data-toggle="tab"><i class="fa fa-code-fork"></i>Timeline</a> --}}
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show in active" id="tab-information">
                        <div class="tab-content">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Information</h4>
                                </div>
                                <div class="box-body">
                                    @displayAll($crud, ['remove' => ['json_type']], ["class" => "col-md-6"])
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="tab-timeline">
                        <div class="tab-content">
                            <div class="box infolist p10">
                                <ul class="timeline timeline-inverse">
                                    <li class="time-label">
                                        <span class="bg-red">
                                        10 Feb. 2014
                                        </span>
                                    </li>

                                    <li>
                                        <i class="fa fa-envelope bg-blue"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                                            <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                                            <div class="timeline-body">
                                                Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                                weebly ning heekya handango imeem plugg dopplr jibjab, movity
                                                jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                                                quora plaxo ideeli hulu weebly balihoo...
                                            </div>
                                            <div class="timeline-footer">
                                                <a class="btn btn-primary btn-xs">Read more</a>
                                                <a class="btn btn-danger btn-xs">Delete</a>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <i class="fa fa-user bg-green"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>
                                            <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
                                        </div>
                                    </li>
                                    
                                    <li>
                                        <i class="fa fa-comments bg-yellow"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>
                                            <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                                            <div class="timeline-body">
                                                Take me to your leader!
                                                Switzerland is small and neutral!
                                                We are more like Germany, ambitious and misunderstood!
                                            </div>
                                            <div class="timeline-footer">
                                                <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    <li class="time-label">
                                        <span class="bg-green">
                                        3 Jan. 2014
                                        </span>
                                    </li>
                                    
                                    <li>
                                        <i class="fa fa-camera bg-purple"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>
                                            <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>
                                            <div class="timeline-body">
                                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                            </div>
                                        </div>
                                    </li>
                                    
                                    <li>
                                        <i class="fa fa-clock-o bg-gray"></i>
                                    </li>
                                </ul>
                            </div>
                        </div>
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