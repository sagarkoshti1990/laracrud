@php
    $access_arr = $access_arr ?? config('stlc.access_list',['view','create','edit','delete','restore','permanently_delete']);
    $modules = $modules ?? \Module::access_modules($item);
    $from_view = $from_view ?? 'show';
    $assessor = $assessor ?? get_class($item);
@endphp
{!! Form::open(['url' => config('stlc.stlc_route_prefix', 'developer').'/modules/access/'.$item->id, 'method' => 'post']) !!}
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="assessor" value="{{$assessor}}">
    {{  Form::hidden('back_url',url($crud->route.'/'.$item->id))  }}
    <div @attributes($crud,$from_view.'.card',['class'=>"card"])>
        <div @attributes($crud,$from_view.'.card_body',['class'=>"card-body list-group-flush"])>
            <table @attributes($crud,$from_view.'.table',['class'=>'table',"id"=>"example"])>
                <thead>
                    <tr>
                        <th>
                            <div @attributes($crud,$from_view.'.form_check',['class'=>'form-check checkbox-inline icheck-primary'])>
                                <input class="check" value="all" type="checkbox" id='all'>
                                <label for='all'><span class="ml-2">All</span></label>
                            </div>
                        </th>
                        @foreach($access_arr as $access)
                            <th class="text-right">
                                <div @attributes($crud,$from_view.'.form_check',['class'=>'form-check checkbox-inline icheck-primary'])>
                                    <input id="{{$access}}" class="check all" value="{{$access}}" type="checkbox">
                                    <label for="{{$access}}"><span class="ml-2">{{ \Str::title(str_replace('_',' ',$access)) }}</span></label>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(collect($modules)->where('type','module') as $key => $module)
                    <tr>
                        <th>
                            <div @attributes($crud,$from_view.'.form_check',['class'=>'form-check checkbox-inline icheck-primary'])>
                                <input id="{{$key.'all'}}" class="check all" value="{{$module->name ?? '' }}" type="checkbox">
                                <label for="{{$key.'all'}}"><span class="ml-2">{{$module->label ?? $module->name }}</span></label>
                            </div>
                        </th>
                        @foreach($access_arr as $access)
                            <td class="text-center">
                                <div @attributes($crud,$from_view.'.form_check',['class'=>'form-check checkbox-inline icheck-primary'])>
                                    <input id="{{$access.$key}}" name="{{ $module->name }}[]" value="{{$access}}" class="{{$access}} {{ $module->name }} all" type="checkbox"
                                        @if(isset($module->accesses) && in_array($access,json_decode(json_encode($module->accesses), true))) checked="checked" @endif
                                    >
                                    <label for="{{$access.$key}}"></label>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @if(collect($modules)->where('type','page')->count() > 0)
                        <tr><td colspan="{{count($access_arr)+1}}"><h1>Pages</h1></td></tr>
                        <tr>
                            <th>
                                <div @attributes($crud,$from_view.'.form_check',['class'=>'form-check checkbox-inline icheck-primary'])>
                                    <input id="all-page" class="check" value="all-page" type="checkbox">
                                    <label for="all-page"><span class="ml-2">All</span></label>
                                </div>
                            </th>
                            <th class="text-center" colspan="{{count($access_arr)}}">
                                <div @attributes($crud,$from_view.'.form_check',['class'=>'form-check checkbox-inline icheck-primary'])>
                                    <input id="view-page" class="check all-page" value="view-page" type="checkbox">
                                    <label for="view-page"><span class="ml-2">View</span></label>
                                </div>
                            </th>
                        </tr>
                        @foreach(collect($modules)->where('type','page') as $key => $module)
                        <tr>
                            <th>
                                <div @attributes($crud,$from_view.'.form_check',['class'=>'form-check checkbox-inline icheck-primary'])>
                                    <input id="{{$key.'all_page'}}" class="check all-page" value="{{$module->name ?? '' }}" type="checkbox">
                                    <label for="{{$key.'all_page'}}"><span class="ml-2">{{$module->label ?? $module->name }}</span></label>
                                </div>
                            </th>
                            <td class="text-center" colspan="{{count($access_arr)}}">
                                <div @attributes($crud,$from_view.'.form_check',['class'=>'form-check checkbox-inline icheck-primary'])>
                                    <input id="{{$access.$key.'page'}}" name="{{ $module->name }}[]" value="view" class="view-page {{ $module->name }} all-page" type="checkbox"
                                        @if(isset($module->accesses) && in_array('view',json_decode(json_encode($module->accesses), true))) checked="checked" @endif
                                    >
                                    <label for="{{$access.$key.'page'}}"></label>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div @attributes($crud,$from_view.'.card_footer',['class'=>"card-footer"])>
            <button @attributes($crud,$from_view.'.button.save',["class"=>'btn btn-primary btn-flat float-right','type'=>"submit"])>Save</button>
        </div>
    </div>
</form>
@pushonce('after_scripts')
<script>
    jQuery(document).ready(function($){
        $('input[type="checkbox"].check').on('change', function (event) {
            var test = event.target.checked;
            console.log(test);
            if (test) {
                $('input[type="checkbox"].' + event.target.value).attr('checked',true);
            } else {
                $('input[type="checkbox"].' + event.target.value).removeAttr('checked');
            }
        });
    });
</script>
@endpushonce
