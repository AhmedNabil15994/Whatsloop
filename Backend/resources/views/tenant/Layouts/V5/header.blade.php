<div class="bgOpacity"></div>

<div class="header clearfix">
    @php
        $currentTime = date('H:i');
        $text = '';
        if($currentTime >= "06:00" && $currentTime <= "11:59"){
            $text = trans('main.morning');
        }elseif($currentTime >= "12:00" && $currentTime <= "17:59"){
            $text = trans('main.afternoon');
        }elseif($currentTime >= "18:00" || $currentTime >= "00:00" && $currentTime <= "05:59" ){
            $text = trans('main.evening');
        }
    @endphp
    <div class="user">
        <i class="icon flaticon-user-3"></i>
{{--         @if(Request::segment(1) == 'dashboard' || Request::segment(1) == 'menu')
 --}}        {{ $text }} {{ FULL_NAME }}
        {{-- @endif --}}
    </div>
    
    <div class="profile">
        <i class="flaticon-menu-1 openProfile"></i>
        <div class="profileStyle">
            <div class="head">
                <i class="fa fa-angle-left iconClose"></i>
                <i class="icon flaticon-user-3"></i>
                <h2 class="name">{{ FULL_NAME }}</h2>
                <span class="account">{{ GROUP_NAME }}</span>
            </div>
            <ul class="listProfile">
                @if(\Helper::checkRules('subscription'))
                <li><a href="{{ URL::to('/profile/subscription') }}"><i class="flaticon-settings-1"></i> {{ trans('main.subscriptionManage') }}</a></li>
                @endif
                @if(\Helper::checkRules('list-invoices'))
                <li><a href="{{ URL::to('/invoices') }}"><i class="flaticon-invoice"></i> {{ trans('main.subs_invoices') }}</a></li>
                @endif
                <li><a href="{{ URL::to('/profile/personalInfo') }}"><i class="flaticon-user-1"></i> {{ trans('main.account_setting') }}</a></li>
                <li><a href="{{ URL::to('/helpCenter') }}"><i class="flaticon-life-buoy"></i> {{ trans('main.helpCenter') }}</a></li>
                <li><a href="{{ URL::to('/logout') }}"><i class="flaticon-user"></i> {{ trans('main.logout') }}</a></li>
            </ul>
            <div class="btnsHeader clearfix">
                @if(DIRECTION == 'ltr')
                <a href="#" class="lang user-langs lang-item" data-next-area="ar">ع</a>
                @else
                <a href="#" class="lang user-langs lang-item" data-next-area="en">EN</a>
                @endif
                <button class="btnDark {{ $mode && $mode->theme == 1 ? 'active' : '' }}" title="Change Mode"></button>
            </div>
        </div>
    </div>

    {{-- <a href="{{ URL::to('/menu') }}" class="iconMenu">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="14" viewBox="0 0 30 14">
          <g id="Group_1327" data-name="Group 1327" transform="translate(-53 -40)">
            <rect id="Rectangle_2268" data-name="Rectangle 2268" width="30" height="2" transform="translate(53 40)"/>
            <rect id="Rectangle_2269" data-name="Rectangle 2269" width="20" height="2" transform="translate(53 46)"/>
            <rect id="Rectangle_2270" data-name="Rectangle 2270" width="15" height="2" transform="translate(53 52)"/>
          </g>
        </svg>
    </a>
 --}}
    @if(DIRECTION == 'ltr')
    <a href="#" class="lang user-langs lang-item" data-next-area="ar">ع</a>
    @else
    <a href="#" class="lang user-langs lang-item" data-next-area="en">EN</a>
    @endif
    <button class="btnDark {{ $mode && $mode->theme == 1 ? 'active' : '' }}" title="Change Mode"></button>
</div>