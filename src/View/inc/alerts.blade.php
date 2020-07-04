{{-- <script src="{{ asset('public/vendor/pnotify/pnotify.custom.min.js') }}"></script> --}}

{{-- Bootstrap Notifications using Prologue Alerts --}}
<script type="text/javascript">
jQuery(document).ready(function($) {

	// PNotify.prototype.options.styling = "bootstrap3";
	// PNotify.prototype.options.styling = "fontawesome";

	@foreach (Alert::getMessages() as $type => $messages)
		@foreach ($messages as $message)
			
			$(function(){
				// new PNotify({
				//   // title: 'Regular Notice',
				//   text: "{{ $message }}",
				//   type: "{{ $type }}",
				//   addclass: 'stack-modal',
				//   icon: true
				// });

				Swal.fire({
					// title: 'Welcome',
					text: "{{ $message }}",
					type: "{{ $type }}",
					showConfirmButton: false,
					timer: 2500
				})
			});

		@endforeach
	@endforeach
});
</script>