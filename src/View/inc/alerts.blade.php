<script type="text/javascript">
jQuery(document).ready(function($) {
	@foreach (Alert::getMessages() as $type => $messages)
		@foreach ($messages as $message)
			$(function(){
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