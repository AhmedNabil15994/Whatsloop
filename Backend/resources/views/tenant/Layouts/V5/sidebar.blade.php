<div class="menuCpanel active">
	<div class="menuHead">
		<i class="iconMenuCpanel">
			<svg xmlns="http://www.w3.org/2000/svg" width="21.58" height="15.174" viewBox="0 0 21.58 15.174">
			  <path id="right-arrow" d="M14.532,4.9a.765.765,0,0,0-1.088,1.077l5.515,5.515H.761A.758.758,0,0,0,0,12.256a.766.766,0,0,0,.761.772h18.2l-5.515,5.5a.781.781,0,0,0,0,1.088.762.762,0,0,0,1.088,0l6.82-6.82a.749.749,0,0,0,0-1.077Z" transform="translate(0 -4.674)" fill="#9499a4"/>
			</svg>
		</i>
		<img src="{{ asset('V5/images/logoWhite.png') }}" alt="" />
	</div>

	<ul class="linksCpanel">

		<li titlehover="link1">
			<a href="{{ URL::to('/dashboard') }}" class="{{ Active(URL::to('/dashboard')) }}"><i class="flaticon-home"></i> {{ trans('main.dashboard') }}</a>
		</li>

		@if(\Helper::checkRules('list-livechat'))
		<li titlehover="link2" id="foo">
			<a href="{{ URL::to('/livechat') }}" class="{{ Active(URL::to('/livechat')) }}"><i class="flaticon-statistics"></i> {{ trans('main.livechat') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('salla-customers,salla-products,salla-orders,salla-abandoned-carts,salla-reports,salla-templates'))
		<li titlehover="link3">
			<a href="#" class="subToggle {{ Active(URL::to('/services/salla*')) }} {{ Active(URL::to('/profile/services?type=salla')) }}"><i class="flaticon-shopping-bag"></i> {{ trans('main.salla') }} <i class="arrowLeft flaticon-left-arrow"></i></a>
			<ul class="subMenu">
				<li><a href="{{ URL::to('/profile/services?type=salla') }}" class="{{ Active(URL::to('/profile/services?type=salla')) }}">{{ trans('main.service_tethering') }}</a></li>
				@if(\Helper::checkRules('salla-customers'))
				<li><a href="{{ URL::to('/services/salla/customers') }}" class="{{ Active(URL::to('/services/salla/customers')) }}">{{ trans('main.customers') }}</a></li>
				@endif
				@if(\Helper::checkRules('salla-products'))
				<li><a href="{{ URL::to('/services/salla/products') }}" class="{{ Active(URL::to('/services/salla/products')) }}">{{ trans('main.products') }}</a></li>
				@endif
				@if(\Helper::checkRules('salla-orders'))
				<li><a href="{{ URL::to('/services/salla/orders') }}" class="{{ Active(URL::to('/services/salla/orders')) }}">{{ trans('main.orders') }}</a></li>
				@endif
				@if(\Helper::checkRules('salla-abandoned-carts'))
				<li><a href="{{ URL::to('/services/salla/abandonedCarts') }}" class="{{ Active(URL::to('/services/salla/abandonedCarts')) }}">{{ trans('main.abandonedCarts') }}</a></li>
				@endif
				@if(\Helper::checkRules('salla-reports'))
				<li><a href="{{ URL::to('/services/salla/reports') }}" class="{{ Active(URL::to('/services/salla/reports')) }}">{{ trans('main.notReports') }}</a></li>
				@endif
				@if(\Helper::checkRules('salla-templates'))
				<li><a href="{{ URL::to('/services/salla/templates') }}" class="{{ Active(URL::to('/services/salla/templates')) }}">{{ trans('main.templates') }}</a></li>
				@endif
			</ul>
		</li>
		@endif

		@if(\Helper::checkRules('zid-customers,zid-products,zid-orders,zid-abandoned-carts,zid-reports,zid-templates'))
		<li titlehover="link4">
			<a class="subToggle {{ Active(URL::to('/services/zid*')) }} {{ Active(URL::to('/profile/services?type=zid')) }}">
				<i>
					<svg xmlns="http://www.w3.org/2000/svg" width="26.903" height="28.284" viewBox="0 0 26.903 28.284">
					  <path id="Path_1246" data-name="Path 1246" d="M119.9,216.65a2.8,2.8,0,0,1,3.242-2.795,16.539,16.539,0,0,1,6.95,3.186,12.453,12.453,0,0,1,4.079,6.494c.444,1.754.746,4.382-.93,5.613-1.471,1.08-2.834-.112-3.191-1.593-.668-2.769.4-5.379,2.073-7.555a16.3,16.3,0,0,1,6.03-4.906,14.048,14.048,0,0,1,4.091-.983,2.343,2.343,0,0,1,2.559,2.338h0a2.343,2.343,0,0,1-2.559,2.339,14.047,14.047,0,0,1-4.091-.984,16.291,16.291,0,0,1-6.03-4.906c-1.677-2.176-2.741-4.785-2.073-7.554.357-1.482,1.72-2.673,3.191-1.593,1.676,1.231,1.374,3.859.93,5.613a12.453,12.453,0,0,1-4.079,6.494,17.042,17.042,0,0,1-7.124,3.352,2.6,2.6,0,0,1-3.068-2.555Z" transform="translate(-118.903 -202.308)" fill="none" stroke="#9499a4" stroke-miterlimit="10" stroke-width="2"/>
					</svg>
				</i> 
				{{ trans('main.zid') }} <i class="arrowLeft flaticon-left-arrow"></i>
			</a>
			<ul class="subMenu">
				<li><a href="{{ URL::to('/profile/services?type=zid') }}" class="{{ Active(URL::to('/profile/services?type=zid')) }}">{{ trans('main.service_tethering') }}</a></li>
				@if(\Helper::checkRules('zid-customers'))
				<li><a href="{{ URL::to('/services/zid/customers') }}" class="{{ Active(URL::to('/services/zid/customers')) }}">{{ trans('main.customers') }}</a></li>
				@endif
				@if(\Helper::checkRules('zid-products'))
				<li><a href="{{ URL::to('/services/zid/products') }}" class="{{ Active(URL::to('/services/zid/products')) }}">{{ trans('main.products') }}</a></li>
				@endif
				@if(\Helper::checkRules('zid-orders'))
				<li><a href="{{ URL::to('/services/zid/orders') }}" class="{{ Active(URL::to('/services/zid/orders')) }}">{{ trans('main.orders') }}</a></li>
				@endif
				@if(\Helper::checkRules('zid-abandoned-carts'))
				<li><a href="{{ URL::to('/services/zid/abandonedCarts') }}" class="{{ Active(URL::to('/services/zid/abandonedCarts')) }}">{{ trans('main.abandonedCarts') }}</a></li>
				@endif
				@if(\Helper::checkRules('zid-reports'))
				<li><a href="{{ URL::to('/services/zid/reports') }}" class="{{ Active(URL::to('/services/zid/reports')) }}">{{ trans('main.notReports') }}</a></li>
				@endif
				@if(\Helper::checkRules('zid-templates'))
				<li><a href="{{ URL::to('/services/zid/templates') }}" class="{{ Active(URL::to('/services/zid/templates')) }}">{{ trans('main.templates') }}</a></li>
				@endif
			</ul>
		
		</li>
		@endif

		@if(\Helper::checkRules('whatsapp-orders,whatsapp-products,whatsapp-coupons,whatsapp-settings'))
		<li titlehover="link5">
			<a  class="subToggle {{ Active( URL::to('/whatsappOrders*') ) }}"><i class="flaticon-layer"></i> {{ trans('main.whatsappOrders') }} <i class="arrowLeft flaticon-left-arrow"></i></a>
			<ul class="subMenu">
				@if(\Helper::checkRules('whatsapp-settings'))
				<li><a href="{{ URL::to('/whatsappOrders/settings') }}" class="{{ Active( URL::to('/whatsappOrders/settings*') ) }}">{{ trans('main.settings') }} </a></li>
				@endif
				@if(\Helper::checkRules('whatsapp-products'))
				<li><a href="{{ URL::to('/whatsappOrders/products') }}" class="{{ Active( URL::to('/whatsappOrders/products*') ) }}">{{ trans('main.products') }} </a></li>
				@endif
				@if(\Helper::checkRules('whatsapp-orders'))
				<li><a href="{{ URL::to('/whatsappOrders/orders') }}" class="{{ Active( URL::to('/whatsappOrders/orders*') ) }}">{{ trans('main.orders') }} </a></li>
				@endif
				@if(\Helper::checkRules('whatsapp-bankTransfers'))
				<li><a href="{{ URL::to('/whatsappOrders/bankTransfers') }}" class="{{ Active( URL::to('/whatsappOrders/bankTransfers*') ) }}">{{ trans('main.transfers') }} </a></li>
				@endif
				@if(\Helper::checkRules('list-coupons'))
				<li><a href="{{ URL::to('/whatsappOrders/coupons') }}" class="{{ Active( URL::to('/whatsappOrders/coupons*') ) }}">{{ trans('main.coupons') }} </a></li>
				@endif
			</ul>
		</li>
		@endif

		@if(\Helper::checkRules('list-bots'))
		<li titlehover="link6">
			<a href="{{ URL::to('/bots') }}" class="{{ Active( URL::to('/bots*') ) }}"><i class="flaticon-robot"></i> {{ trans('main.chatBot') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-bots-plus'))
		<li titlehover="link6">
			<a href="{{ URL::to('/botPlus') }}" class="{{ Active( URL::to('/botPlus*') ) }}"><i class="flaticon-robot"></i> {{ trans('main.botPlus') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-templates'))
		<li titlehover="link7">
			<a href="{{ URL::to('/templates') }}" class="{{ Active( URL::to('/templates*') ) }}"><i class="flaticon-layer"></i> {{ trans('main.templates') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-replies'))
		<li titlehover="link8">
			<a href="{{ URL::to('/replies') }}" class="{{ Active( URL::to('/replies*') ) }}"><i class="flaticon-chat-bubble"></i> {{ trans('main.replies') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-categories'))
		<li titlehover="link9">
			<a href="{{ URL::to('/categories') }}" class="{{ Active( URL::to('/categories*') ) }}"><i class="flaticon-menu"></i> {{ trans('main.categories') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-contacts'))
		<li titlehover="link10">
			<a href="{{ URL::to('/contacts') }}" class="{{ Active( URL::to('/contacts*') ) }}"><i class="flaticon-send"></i> {{ trans('main.contacts') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-group-numbers,add-number-to-group,list-groupNumberReports'))
		<li titlehover="link11">
			<a href="#" class="subToggle {{ Active( URL::to('/groupNumbers')) }} {{  Active( URL::to('/addGroupNumbers') ) }} {{ Active( URL::to('/groupNumberReports') ) }}"><i class="flaticon-add-group"></i> {{ trans('main.groupNumbers') }} <i class="arrowLeft flaticon-left-arrow"></i></a>
			<ul class="subMenu">
				@if(\Helper::checkRules('list-group-numbers'))
				<li><a href="{{ URL::to('/groupNumbers') }}">{{ trans('main.groupNumbers') }}</a></li>
				@endif
				@if(\Helper::checkRules('add-number-to-group'))
				<li><a href="{{ URL::to('/addGroupNumbers') }}">{{ trans('main.addGroupNumbers') }}</a></li>
				@endif
				@if(\Helper::checkRules('list-groupNumberReports'))
				<li><a href="{{ URL::to('/groupNumberReports') }}">{{ trans('main.groupNumberRepors') }}</a></li>
				@endif
			</ul>
		</li>
		@endif

        @if(\Helper::checkRules('list-group-messages,add-group-message'))
        <li titlehover="link12">
			<a href="#" class="subToggle {{ Active( URL::to('/groupMsgs*') ) }}"><i class="flaticon-edit"></i> {{ trans('main.groupMsgs') }} <i class="arrowLeft flaticon-left-arrow"></i></a>
			<ul class="subMenu">
				@if(\Helper::checkRules('list-group-messages'))
				<li><a href="{{ URL::to('/groupMsgs') }}" class="{{ Active( URL::to('/groupMsgs') ) }}">{{ trans('main.groupMsgs') }}</a></li>
				@endif
				@if(\Helper::checkRules('add-group-message'))
				<li><a href="{{ URL::to('/groupMsgs/add') }}" class="{{ Active( URL::to('/groupMsgs/add') ) }}">{{ trans('main.sendNewMessage') }}</a></li>
				@endif
			</ul>
		</li>
		@endif

		@if(\Helper::checkRules('list-statuses'))
		<li titlehover="link13">
			<a href="{{ URL::to('/statuses') }}" class="{{ Active( URL::to('/statuses*') ) }}"><i class="flaticon-mode"></i> {{ trans('main.statuses') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-messages-archive'))
		<li titlehover="link14">
			<a href="{{ URL::to('/msgsArchive') }}" class="{{ Active( URL::to('/msgsArchive*') ) }}"><i class="flaticon-inbox"></i> {{ trans('main.groupMsgsArc') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-tickets'))
		<li titlehover="link15">
			<a href="{{ URL::to('/tickets') }}" class="{{ Active( URL::to('/tickets*') ) }}"><i class="flaticon-tag"></i> {{ trans('main.tickets') }}</a>
		</li>
		@endif
		
		@if(\Helper::checkRules('list-storage'))
		<li titlehover="link16">
			<a href="{{ URL::to('/storage') }}" class="{{ Active( URL::to('/storage*') ) }}"><i class="flaticon-folder"></i> {{ trans('main.storage') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-invoices'))
		<li titlehover="link17">
			<a href="{{ URL::to('/invoices') }}" class="{{ Active( URL::to('/invoices*') ) }}"><i class="flaticon-invoice"></i> {{ trans('main.subs_invoices') }}</a>
		</li>
		@endif

		@if(\Helper::checkRules('list-users,list-groups'))
        <li titlehover="link18">
			<a href="#" class="subToggle {{ Active(URL::to('/users*')) }} {{ Active(URL::to('/groups*')) }}"><i class="flaticon-group"></i> {{ trans('main.users') }} <i class="arrowLeft flaticon-left-arrow"></i></a>
			<ul class="subMenu">
				@if(\Helper::checkRules('list-users'))
				<li><a href="{{ URL::to('/users') }}" class="{{ Active(URL::to('/users*')) }}">{{ trans('main.users') }}</a></li>
				@endif
				@if(\Helper::checkRules('list-groups'))
				<li><a href="{{ URL::to('/groups') }}" class="{{ Active(URL::to('/groups*')) }}">{{ trans('main.groups') }}</a></li>
				@endif
			</ul>
		</li>
		@endif

		@if(\Helper::checkRules('apiSetting,apiGuide,webhookSetting'))
        <li titlehover="link19">
			<a href="#" class="subToggle {{ Active(URL::to('/profile*') && (Request::segment(1) == 'profile' && Request::segment(2) != 'personalInfo')) }}"><i class="flaticon-settings"></i> {{ trans('main.website_setting') }} <i class="arrowLeft flaticon-left-arrow"></i></a>
			<ul class="subMenu">
				@if(\Helper::checkRules('apiSetting'))
				<li><a href="{{ URL::to('/profile/apiSetting') }}" class="{{ Active(URL::to('/profile/apiSetting')) }}">{{ trans('main.api_setting') }}</a></li>
				@endif
				@if(\Helper::checkRules('apiGuide'))
				<li><a href="{{ URL::to('/profile/apiGuide') }}" class="{{ Active(URL::to('/profile/apiGuide')) }}">{{ trans('main.api_guide') }}</a></li>
				@endif
				@if(\Helper::checkRules('webhookSetting'))
				<li><a href="{{ URL::to('/profile/webhookSetting') }}" class="{{ Active(URL::to('/profile/webhookSetting')) }}">{{ trans('main.webhook_setting') }}</a></li>
				@endif
			</ul>
		</li>
		@endif

		
		<li titlehover="link20">
			<a href="{{ URL::to('/profile/personalInfo') }}" class="{{ Active( URL::to('/profile/personalInfo*') ) }}"><i class="flaticon-pages"></i> {{ trans('main.account_setting') }}</a>
		</li>
		<li titlehover="link21">
			<a href="{{ URL::to('/helpCenter') }}" class="{{ Active( URL::to('/helpCenter*') ) }}"><i class="flaticon-life-buoy"></i> {{ trans('main.helpCenter') }}</a>
		</li>
		<li titlehover="link22">
			<a href="{{ URL::to('/logout') }}"><i class="flaticon-user"></i> {{ trans('main.logout') }}</a>
		</li>
	</ul>



	<div class="hovers">
		<ul>
			<li class="titleHover" id="link1"><a href="{{ URL::to('/dashboard') }}" class="link">{{ trans('main.dashboard') }}</a></li>
			<li class="titleHover" id="link2"><a href="{{ URL::to('/livechat') }}" class="link">{{ trans('main.livechat') }}</a></li>
			<li class="titleHover" id="link3"><a href="{{ URL::to('/profile/services?type=salla') }}" class="link">{{ trans('main.salla') }}</a></li>
			<li class="titleHover" id="link4"><a href="{{ URL::to('/profile/services?type=zid') }}" class="link">{{ trans('main.zid') }}</a></li>
			<li class="titleHover" id="link5"><a href="{{ URL::to('/whatsappOrders/orders') }}" class="link">{{ trans('main.whatsappOrders') }}</a></li>
			<li class="titleHover" id="link6"><a href="{{ URL::to('/bots') }}" class="link">{{ trans('main.bots') }}</a></li>
			<li class="titleHover" id="link7"><a href="{{ URL::to('/templates') }}" class="link">{{ trans('main.templates') }}</a></li>
			<li class="titleHover" id="link8"><a href="{{ URL::to('/replies') }}" class="link">{{ trans('main.replies') }}</a></li>
			<li class="titleHover" id="link9"><a href="{{ URL::to('/categories') }}" class="link">{{ trans('main.categories') }}</a></li>
			<li class="titleHover" id="link10"><a href="{{ URL::to('/contacts') }}" class="link">{{ trans('main.contacts') }}</a></li>
			<li class="titleHover" id="link11"><a href="{{ URL::to('/groupNumbers') }}" class="link">{{ trans('main.groupNumbers') }}</a></li>
			<li class="titleHover" id="link12"><a href="{{ URL::to('/groupMsgs') }}" class="link">{{ trans('main.groupMsgs') }}</a></li>
			<li class="titleHover" id="link13"><a href="{{ URL::to('/statuses') }}" class="link">{{ trans('main.statuses') }}</a></li>
			<li class="titleHover" id="link14"><a href="{{ URL::to('/msgsArchive') }}" class="link">{{ trans('main.msgsArchive') }}</a></li>
			<li class="titleHover" id="link15"><a href="{{ URL::to('/tickets') }}" class="link">{{ trans('main.tickets') }}</a></li>
			<li class="titleHover" id="link16"><a href="{{ URL::to('/storage') }}" class="link">{{ trans('main.storage') }}</a></li>
			<li class="titleHover" id="link17"><a href="{{ URL::to('/invoices') }}" class="link">{{ trans('main.invoices') }}</a></li>
			<li class="titleHover" id="link18"><a href="{{ URL::to('/users') }}" class="link">{{ trans('main.users') }}</a></li>
			<li class="titleHover" id="link19"><a href="{{ URL::to('/profile/apiSetting') }}" class="link">{{ trans('main.apiSetting') }}</a></li>
			<li class="titleHover" id="link20"><a href="{{URL::to('/profile/personalInfo')}}" class="link">{{ trans('main.account_setting') }}</a></li>
			<li class="titleHover" id="link21"><a href="{{ URL::to('/helpCenter') }}" class="link">{{ trans('main.helpCenter') }}</a></li>
			<li class="titleHover" id="link22"><a href="{{ URL::to('/logout') }}" class="link">{{ trans('main.logout') }}</a></li>
		</ul>
	</div>
</div>