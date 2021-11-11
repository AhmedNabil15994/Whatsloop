<div class="{{ in_array( Request::segment(1),['postBundle','checkout','packages']) && (Request::segment(1) != 'invoices' && Request::segment(4) != 'checkout')  && (Request::segment(1) != 'profile' && Request::segment(3) != 'transferPayment') ? 'mybreadcrumb' : 'breadCrumb'  }}">
  	<ul class="{{ in_array( Request::segment(1),['postBundle','checkout','packages']) && (Request::segment(1) != 'invoices' && Request::segment(4) != 'checkout')  && (Request::segment(1) != 'profile' && Request::segment(3) != 'transferPayment') ? 'listBread' : 'list clearfix' }}">
  		<li><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
  		<li>@yield('title')</li>
  	</ul>
    @yield('ExtraBreadCrumb')
</div>