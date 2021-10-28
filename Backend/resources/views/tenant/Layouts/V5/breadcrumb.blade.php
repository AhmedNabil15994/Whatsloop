<div class="{{ in_array( Request::segment(1),['postBundle','checkout','packages','updateSubscription']) ? 'mybreadcrumb' : 'breadCrumb'  }}">
  	<ul class="{{ in_array( Request::segment(1),['postBundle','checkout','packages','updateSubscription']) ? 'listBread' : 'list clearfix' }}">
  		<li><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
  		<li>@yield('title')</li>
  	</ul>
    @yield('ExtraBreadCrumb')
</div>