<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
	<div class="left-content" style="margin-top: 15px;">
		@if(Request::segment(1) != 'dashboard')
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
				<li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
			</ol>
		</nav>
		@endif
		<h4 class="content-title mb-2">
		@php
			$currentTime = date('H:i');
			$text = '';
			if($currentTime >= "06:00" && $currentTime <= "11:59"){
				$text = trans('main.morning');
			}elseif($currentTime >= "12:00" && $currentTime <= "17:59"){
				$text = trans('main.afternoon');
			}elseif($currentTime >= "18:00" && $currentTime <= "05:59"){
				$text = trans('main.evening');
			}
		@endphp
		@if(Request::segment(1) == 'dashboard')
		{{ $text }} {{ FULL_NAME }}
		@endif
		</h4>
	</div>
	<div class="d-flex my-auto right-content">
		
	</div>
</div>
<!-- /breadcrumb -->