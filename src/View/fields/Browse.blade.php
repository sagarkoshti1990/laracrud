<!-- browse server input -->

<div @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_wrapper_attributes',['field_name' => $field['name']]) >
	@if((isset($field['attributes']['label']) && $field['attributes']['label']) || !isset($field['attributes']['label']))
		<label for="{{ $field['name'] }}" class="control-label">{!! $field['label'] !!}</label>
		@include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_translatable_icon')
	@endif
	<input
		type="text"
		id="{{ $field['name'] }}-filemanager"

		name="{{ $field['name'] }}"
        value="{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}"
        @include(config('stlc.stlc_modules_folder_name','stlc::').'inc.field_attributes')

		@if(!isset($field['readonly']) || $field['readonly']) readonly @endif
	>

	<div class="btn-group" role="group" aria-label="..." style="margin-top: 3px;">
	  <button type="button" data-inputid="{{ $field['name'] }}-filemanager" class="btn btn-default popup_selector">
		<i class="fa fa-cloud-upload"></i> {{ trans('crud.browse_uploads') }}</button>
		<button type="button" data-inputid="{{ $field['name'] }}-filemanager" class="btn btn-default clear_elfinder_picker">
		<i class="fa fa-eraser"></i> {{ trans('crud.clear') }}</button>
	</div>

    @if ($errors->has($field['name']))
        <span class="help-block">{{ $errors->first($field['name']) }}</span>
	@endif
	
	@if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>
@pushonce('crud_fields_styles')
	<!-- include browse server css -->
	<link href="{{ asset('public/vendor/lara/colorbox/example2/colorbox.css') }}" rel="stylesheet" type="text/css" />
	<style>
		#cboxContent, #cboxLoadedContent, .cboxIframe {
			background: transparent;
		}
	</style>
@endpushonce

@pushonce('crud_fields_scripts')
	<!-- include browse server js -->
	<script src="{{ asset('public/vendor/lara/colorbox/jquery.colorbox-min.js') }}"></script>
	<script>
		$(document).on('click','.popup_selector[data-inputid={{ $field['name'] }}-filemanager]',function (event) {
			event.preventDefault();
		    // trigger the reveal modal with elfinder inside
			var triggerUrl = "{{ url(config('elfinder.route.prefix').'/popup/'.$field['name']."-filemanager") }}";
			$.colorbox({
				href: triggerUrl,
				fastIframe: true,
				iframe: true,
				width: '80%',
				height: '80%'
			});
		});

		// function to update the file selected by elfinder
		function processSelectedFile(filePath, requestingField) {
			$('#' + requestingField).val(filePath);
		}

		$(document).on('click','.clear_elfinder_picker[data-inputid={{ $field['name'] }}-filemanager]',function (event) {
			event.preventDefault();
			var updateID = $(this).attr('data-inputid'); // Btn id clicked
			$("#"+updateID).val("");
		});
	</script>
@endpushonce