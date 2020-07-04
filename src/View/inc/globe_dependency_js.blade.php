<script type="text/javascript">
    $(document).ready(function ($) {
        $('.state_select').each(function (index, element) {
            (function(that, i) { 
            var t = setTimeout(function() { 
                loadcities(element);
            }, 500 * i);
        })(this, index);;
            // $.when(loadcities(element)).done();
        });
        $('.city_select').on('change',function(e){
            $(this).attr('input_value', e.currentTarget.value);
        });
    });
    var form_id = '{{ isset($form_id) ? "#".$form_id : "" }}';

    // function loadStates(state = $('.state_select'),city=$('.city_select')) {

    //     var option_name = (typeof $(state).data('option_name') !== 'undefined') ? $(state).data('option_name') : 'Select State';
    //     var state_element = state;

    //     $.ajax({
    //         url: '{{url("load-states/")}}',
    //         type: 'get',
    //         dataType: 'json',
    //         success: function (result) {
    //             // console.log(result);
    //             var html = '<option value="">' + option_name + '</option>';
    //             if (result.message == "found") {
    //                 for (var i = 0; i < result.states.length; i++) {
    //                     var state = result.states[i];
    //                     html += '<option value="' + state.id + '">' + state.name + '</option>';
    //                 }
    //                 $(state_element).html(html);
    //                 var state_id = "{{old('state_id') ?? $state_id ?? ''}}";
    //                 if (isset(state_id) && state_id != "") {
    //                     // $(form_id + " :input[name='']").val(state_id).change();
    //                     loadcities(state_element,city);
    //                     // console.log(state_id);
    //                 }
    //                 loadcities(state_element,city);
    //             } else if (result.message == "empty") {
    //                 $(form_id + " select[name='state_id']").html('<option value="">No State Found</option>');
    //             }
    //         }
    //     });
    // }

    function loadcities(state = null,city = null,value=null) {
        city_elem = (typeof city !== 'undefined' && city !== null) ? city : $(state).parents('.globe_dependency').find('.city_select');
        var option_name = (typeof $(city_elem).data('option_name') !== 'undefined') ? $(city_elem).data('option_name') : 'Select city';
        value = (typeof $(city_elem).attr('input_value') !== 'undefined') ? $(city_elem).attr('input_value') : null;
        // console.log(state.value !== "");

        if (state != null && state.value !== "") {
            var state_id = $(state).val();
            var html = '<option value="">' + option_name + '</option>';
            if (isset(state_id) && state_id != "") {
                // alert(1);
                $.ajax({
                    url: '{{url("load-cities")}}' + '/' + state_id,
                    type: 'get',
                    dataType: 'json',
                    success: function (result) {
                        if (result.message == "found") {
                            for (var i = 0; i < result.cities.length; i++) {
                                var city = result.cities[i];
                                var selected = "";
                                if(isset(value) && value != "" && city.id == value) {
                                    selected = "selected";
                                }
                                html += '<option value="' + city.id + '" '+selected+'>' + city.name + '</option>';
                            }
                            
                            $(city_elem).html(html);
                            // console.log(city_elem);
                            if (isset(state_id) && state_id != "") {
                                var city_id = "{{old('city_id') ?? $city_id ?? ''}}";
                                if (isset(city_id) && city_id != "") {
                                    $(city_elem).val(city_id);
                                }
                            }
                        } else if (result.message == "empty") {
                            //alert(3);
                            $(city_elem).html('<option value="">No city Found</option>');
                        }
                    }
                });
            }
        } else {
            $(city_elem).html('<option value="">No city Found</option>');
            {{-- var state_id = $(form_id + " select[name='']").val();
            // var html = '<option value=""></option>';
            // if (isset(state_id) && state_id != "") {
            //     $.ajax({
            //         url: '{{url("load-cities/")}}' + '/' + state_id,
            //         type: 'get',
            //         dataType: 'json',
            //         success: function (result) {
            //             if (result.message == "found") {
            //                 for (var i = 0; i < result.cities.length; i++) {
            //                     var city = result.cities[i];
            //                     html += '<option value="' + city.id + '">' + city.name + '</option>';
            //                 }
            //                 $(form_id + " select[name='']").html(html);
            //                 if (isset(state_id) && state_id != "") {
            //                     var city_id = "{{old('city_id') ?? $city_id ?? ''}}";
            //                     if (isset(city_id) && city_id != "") {
            //                         $(form_id + " :input[name='']").val(city_id).change();
            //                     }
            //                 }
            //             } else if (result.message == "empty") {
            //                 $(form_id + " select[name='']").html(
            //                     '<option value="">No city Found</option>');
            //             }
            //         }
            //     });
            // }
            --}}
        }
    }
</script>