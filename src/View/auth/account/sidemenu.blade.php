<div class="box px-3 pb-1 pt-2">
    <div class="box-body box-profile text-center">
		<img class="profile-user-img img-responsive rounded-circle" src="{{ Auth::user()->profile_pic() }}">
		<h3 class="profile-username text-center">{{ auth()->user()->name }}</h3>
		<span><h6>Roles</h6></span>
		@if(!isset(auth()->user()->context()->id))
			<span class="badge large bg-red text-center" style="font-size:12px;">No Context Found</span>
		@endif
		{{--  @foreach(Auth::user()->roles as $role)  --}}
		{{--  <span class="badge large bg-purple text-center">{{ $role->label }}</span>  --}}
		@forelse(\Auth::user()->roles as $role)
				<span class="badge large bg-purple text-center" style="font-size:12px;">{{ $role->label }}</span>
		@empty
				<span class="badge large bg-red text-center" style="font-size:12px;">Not assing</span>
		@endforelse
	</div>
	<hr class="mt-3 mb-3">
	<ul class="nav nav-pills nav-fill pb-3">
		<li class="nav-item">
			<a class="nav-link @if(Request::route()->getName()=='stlc.account.info') active @endif" href="{{ route('stlc.account.info') }}">Show Account Info</a>
		</li>
		<li class="nav-item">
			<a class="nav-link @if(Request::route()->getName()=='stlc.account.password') active @endif" href="{{ route('stlc.account.password') }}">Change Password</a>
		</li>
	</ul>
</div>
