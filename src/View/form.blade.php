@extends(config('stlc.stlc_layout_path','stlc::layouts.app'))
@section('header')
    @php
        $route = (isset($crud->row->id) && !isset($_GET['copy'])) ? $crud->route.'/'.$crud->row->id : $crud->route;
        $method = (isset($crud->row->id) && !isset($_GET['copy'])) ? 'put' : 'post';
        $form_id = (isset($crud->row->id) && !isset($_GET['copy'])) ? 'edit_form' : 'add_form';
        $add_edit = (isset($crud->row->id) && !isset($_GET['copy'])) ? trans('stlc.edit') : trans('stlc.add');
    @endphp
    @include(config('stlc.view_path.inc.header','stlc::inc.header'),['sub_headeing'=>$add_edit,'title_link' => true])
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        {!! Form::open($crud->getViewAtrributes('form.submit_form',['url' => $route, 'method' => $method, 'id' => $form_id])) !!}
            <div @attributes($crud,'form.card',['class'=>"card"])>
                <div @attributes($crud,'form.card_header',['class'=>"card-header d-none"])>
                    <h4 class="card-title">{{ $add_edit }} {{ $crud->label }}</h4>
                </div>
                <div @attributes($crud,'form.card_body',['class'=>"card-body"])>
                    @if(isset($src))
                        {{ Form::hidden('src', $src) }}
                    @endif
                    @form($crud, [], ["class" => "col-md-6"])
                </div>
                <div @attributes($crud,'form.card_footer',['class'=>"card-footer"])>
                    @include(config('stlc.view_path.inc.form_save_buttons','stlc::inc.form_save_buttons'),['from_view'=>'form'])
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection
@push('after_scripts')
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#{{$form_id}}').validate();
        });
    </script>
@endpush