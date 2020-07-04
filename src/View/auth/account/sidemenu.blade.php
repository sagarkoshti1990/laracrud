<div class="box p15 pb5 pt10">
    <div class="box-body box-profile">
	    <img class="profile-user-img img-responsive img-circle" src="{{ Auth::user()->profile_pic() }}">
			<h3 class="profile-username text-center">{{ auth()->user()->name }}</h3>
			<span><h6>Roles</h6></span>
			@if(!isset(auth()->user()->context()->id))
				<span class="label large bg-red text-center" style="font-size:12px;">No Context Found</span>
			@endif
			{{--  @foreach(Auth::user()->roles as $role)  --}}
			{{--  <span class="label large bg-purple text-center">{{ $role->label }}</span>  --}}
			@forelse(\Auth::user()->roles as $role)
					<span class="label large bg-purple text-center" style="font-size:12px;">{{ $role->label }}</span>
			@empty
					<span class="label large bg-red text-center" style="font-size:12px;">Not assing</span>
			@endforelse
	</div>

	<hr class="mt5 mb10">

	<ul class="nav nav-pills nav-stacked pb15">

	  <li role="presentation"
		@if (Request::route()->getName() == 'stlc.account.info')
	  	class="active"
	  	@endif
	  	><a href="{{ route('stlc.account.info') }}">{{ trans('base.show_account_info') }}</a></li>

	  <li role="presentation"
		@if (Request::route()->getName() == 'stlc.account.password')
	  	class="active"
	  	@endif
	  	><a href="{{ route('stlc.account.password') }}">{{ trans('base.change_password') }}</a></li>

	</ul>
</div>
