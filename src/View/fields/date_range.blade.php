<!-- bootstrap daterange picker input -->

@php
    // if the column has been cast to Carbon or Date (using attribute casting)
    // get the value as a date string
    if (isset($field['value']) && ( $field['value'] instanceof \Carbon\Carbon || $field['value'] instanceof \Jenssegers\Date\Date )) {
        $field['value'] = $field['value']->format( 'Y-m-d H:i:s' );
    }

    //Do the same as the above but for the range end field
    if ( isset($entry) && ($entry->{$field['end_name']} instanceof \Carbon\Carbon || $entry->{$field['end_name']} instanceof \Jenssegers\Date\Date) ) {
        $end_name = $entry->{$field['end_name']}->format( 'Y-m-d H:i:s' );
    } else {
        $end_name = null;
    }
@endphp

<div @include('crud.inc.field_wrapper_attributes',['field_name' => $field['name']]) >
    <input
        type="hidden"
        class="date_range"
        name="{{ $field['name'] }}"
        value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
    >
    {{--  <input class="datepicker-range-start" type="hidden" name="{{ $field['start_name'] }}" value="{{ old($field['start_name']) ? old($field['start_name']) : (isset($field['value']) ? $field['value'] : (isset($field['start_default']) ? $field['start_default'] : '' )) }}">
    <input class="datepicker-range-end" type="hidden" name="{{ $field['end_name'] }}" value="{{ old($field['end_name']) ? old($field['end_name']) : (!empty($end_name) ? $end_name : (isset($field['end_default']) ? $field['end_default'] : '' )) }}">  --}}
    @if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
        <label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
    @endif
    <div class="input-group date">
        <input
            data-bs-daterangepicker="{{ isset($field['date_range_options']) ? json_encode($field['date_range_options']) : '{}'}}"
            type="text"
            @include('crud.inc.field_attributes')
            >
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
        </div>
    </div>

    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
    @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
 @if ($crud->checkIfOnce($field))
 

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
    <link rel="stylesheet" href="{{ asset('node_modules/admin-lte/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
    <script src="{{ asset('node_modules/admin-lte/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('node_modules/admin-lte/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        jQuery(document).ready(function($){
            $('[data-bs-daterangepicker]').each(function(){
                var $fake = $(this);
                $fake.daterangepicker({
                    showDropdowns: true,
                    @if(isset($field['value']))
                        startDate : moment("{{ json_decode($field['value'])->start }}").format('DD/MM/YYYY'),
                        endDate : moment("{{ json_decode($field['value'])->end }}").format('DD/MM/YYYY'),
                    @else
                        // startDate : moment(),
                        // endDate : moment().add(29, 'days');
                        autoUpdateInput:false,
                    @endif
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
                }, 
                function(start, end, label) {
                    $fake.val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                    $fake.parents('.form-group').find('input[name="{{ $field['name'] }}"]').val(JSON.stringify({start:start.format('YYYY-MM-DD'),end:end.format('YYYY-MM-DD')}));
                });
                $picker = $fake.data('daterangepicker');
                $fake.on('keydown', function(e){
                    e.preventDefault();
                    return false;
                });
                $fake.on('apply.daterangepicker hide.daterangepicker', function(e, picker){
                    // $start.val( picker.startDate.format('YYYY-MM-DD') );
                    // $end.val( picker.endDate.format('YYYY-MM-DD') );
                    $fake.parents('.form-group').find('input[name="{{ $field['name'] }}"]').val(JSON.stringify({start:picker.startDate.format('YYYY-MM-DD'),end:picker.endDate.format('YYYY-MM-DD')}));
                });

            });
        });
    </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
