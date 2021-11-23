<div class="footer">
	<p class="copyrights">{{ trans('main.rights') }} - &copy; {{ trans('main.appName') }} {{ date('Y') }}</p>
	<i class="top back-top">
		<svg xmlns="http://www.w3.org/2000/svg" width="27.759" height="25.586" viewBox="0 0 27.759 25.586">
		  <g id="Group_1367" data-name="Group 1367" transform="translate(-36.121 -1712.53)">
		    <path id="Path_915" data-name="Path 915" d="M6601,4290.526,6614.525,4277l13.526,13.526" transform="translate(-6564.526 -2563.763)" fill="none" stroke="#fff" stroke-width="1"/>
		    <path id="Path_916" data-name="Path 916" d="M6601,4290.526,6614.525,4277l13.526,13.526" transform="translate(-6564.526 -2552.763)" fill="none" stroke="#fff" stroke-width="1"/>
		  </g>
		</svg>
	</i>
</div>

<div class="menuDownHeight"></div>
<div class="menuDown">
	<ul class="linksList">
		<li><a href="{{ URL::to('/dashboard') }}"><i class="flaticon-home"></i>{{ trans('main.dashboard') }}</a></li>
		@if(\Helper::checkRules('list-livechat'))
		<li><a href="{{ URL::to('/livechat') }}"><i class="flaticon-statistics"></i>{{ trans('main.dialogs') }}</a></li>
		@endif
		<li><a href="{{ URL::to('/profile/personalInfo') }}" class="openProfile"><i class="flaticon-user-3"></i>{{ trans('main.myAccount') }}</a></li>
		<li><a class="iconMenuCpanel"><i class="fa fa-bars"></i>{{ trans('main.menu1') }}</a></li>
	</ul>
</div>