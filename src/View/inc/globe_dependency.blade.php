@php
    $state_label_name = isset($state_label_name) ? $state_label_name : 'State';
    $state_input_name = isset($state_input_name) ? $state_input_name : 'state_id';
    $city_label_name = isset($city_label_name) ? $city_label_name : 'City';
    $city_input_name = isset($city_input_name) ? $city_input_name : 'city_id';

    $state_input_value = isset($state_input_value) ? $state_input_value : null;
    $city_input_value = isset($city_input_value) ? $city_input_value : null;

    $option_name_state = isset($option_name_state) ? $option_name_state : 'Select State';
    $option_name_city = isset($option_name_city) ? $option_name_city : 'Select city';

    $required_state = isset($required_state) ? $required_state : '';
    $required_city = isset($required_city) ? $required_city : '';
@endphp
<div class="row globe_dependency">
    <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>
        <div class="form-group padd-location">
            <label for="{{$state_input_name}}" class="control-label">{{$state_label_name}} {!! ($required_state == 'required') ? '<span style="color:red;">*</span>' : '' !!}</label>
            <select
                name="{{$state_input_name}}"
                class="padd-location form-control selectstate-city state_select"
                data-option_name="{{$option_name_state}}"
                onchange="loadcities(this)"
                style="width:100%;"
                {{ $required_state }}
            >
                <option value="">{{$option_name_state}}</option>
                @foreach(App\Models\State::all() as $state)
                    <option
                        value="{{$state->id}}"
                        @if(isset($state_input_value) && $state_input_value == $state->id)
                        selected
                        @endif
                    >{{$state->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class='col-lg-6 col-md-6 col-sm-6 col-xs-12'>
        <div class="form-group padd-location">
            <label for="{{$city_input_name}}" class="control-label">{{$city_label_name}} {!! ($required_city == 'required') ? '<span style="color:red;">*</span>' : '' !!}</label>
            <select
                name="{{$city_input_name}}"
                class="padd-location form-control selectstate-city city_select"
                data-option_name="{{$option_name_city}}"
                input_value="{{$city_input_value ?? null}}"                
                {{ $required_city }}
                style="width:100%;"
            >
                <option value="">{{$option_name_city}}</option>
            </select>
        </div>
    </div>
</div>
