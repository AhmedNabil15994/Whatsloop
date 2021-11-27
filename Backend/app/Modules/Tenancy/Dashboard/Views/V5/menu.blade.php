@extends('tenant.Layouts.V5.master2')
@section('title',trans('main.menu'))
@section('styles')

@endsection


{{-- Content --}}
@section('content')
<!-- row -->

<div class="categories clearfix">
    <div class="row">
        <div class="linkStyle">
            <a href="{{ URL::to('/dashboard') }}" class="link">
                <i class="icon flaticon-home"></i> {{ trans('main.dashboard') }}
            </a>
        </div>
        @if(\Helper::checkRules('list-livechat'))
        <div class="linkStyle">
            <a href="{{ URL::to('/livechat') }}" class="link">
                <i class="icon flaticon-statistics"></i> {{ trans('main.livechat') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('salla-customers,salla-products,salla-orders,salla-abandoned-carts,salla-reports,salla-templates'))
        <div class="linkStyle">
            <a class="link">
                <i class="icon flaticon-shopping-bag"></i>
                {{ trans('main.salla') }}
                <i class="openSub flaticon-menu-1"></i>
            </a>
            <ul class="subMenu">
                <li><a href="{{ URL::to('/profile/services?type=salla') }}">{{ trans('main.service_tethering') }} <i class="flaticon-left-arrow"></i></a></li>
                @if(\Helper::checkRules('salla-customers'))
                <li><a href="{{ URL::to('/services/salla/customers') }}">{{ trans('main.customers') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('salla-products'))
                <li><a href="{{ URL::to('/services/salla/products') }}">{{ trans('main.products') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('salla-orders'))
                <li><a href="{{ URL::to('/services/salla/orders') }}">{{ trans('main.orders') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('salla-abandoned-carts'))
                <li><a href="{{ URL::to('/services/salla/abandonedCarts') }}">{{ trans('main.abandonedCarts') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('salla-reports'))
                <li><a href="{{ URL::to('/services/salla/reports') }}">{{ trans('main.notReports') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('salla-templates'))
                <li><a href="{{ URL::to('/services/salla/templates') }}">{{ trans('main.templates') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
            </ul>
        </div>
        @endif
        @if(\Helper::checkRules('zid-customers,zid-products,zid-orders,zid-abandoned-carts,zid-reports,zid-templates'))
        <div class="linkStyle">
            <a class="link">
                <i class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="31.158" height="32.769" viewBox="0 0 31.158 32.769">
                      <path id="Path_1275" data-name="Path 1275" d="M119.9,218.925a3.281,3.281,0,0,1,3.8-3.273,19.364,19.364,0,0,1,8.138,3.73,14.581,14.581,0,0,1,4.776,7.6c.52,2.054.873,5.131-1.089,6.572-1.722,1.265-3.318-.131-3.736-1.865-.782-3.242.464-6.3,2.427-8.846a19.086,19.086,0,0,1,7.06-5.744,16.448,16.448,0,0,1,4.79-1.151,2.743,2.743,0,0,1,3,2.738h0a2.743,2.743,0,0,1-3,2.739,16.447,16.447,0,0,1-4.79-1.152,19.075,19.075,0,0,1-7.06-5.744c-1.964-2.548-3.209-5.6-2.427-8.845.418-1.735,2.014-3.13,3.736-1.865,1.962,1.441,1.609,4.518,1.089,6.572a14.581,14.581,0,0,1-4.776,7.6,19.954,19.954,0,0,1-8.341,3.925,3.047,3.047,0,0,1-3.592-2.992Z" transform="translate(-118.903 -202.306)" fill="none" stroke="#9499a4" stroke-miterlimit="10" stroke-width="2"/>
                    </svg>
                </i>
                {{ trans('main.zid') }}
                <i class="openSub flaticon-menu-1"></i>
            </a>
            <ul class="subMenu">
                <li><a href="{{ URL::to('/profile/services?type=zid') }}">{{ trans('main.service_tethering') }} <i class="flaticon-left-arrow"></i></a></li>
                @if(\Helper::checkRules('zid-customers'))
                <li><a href="{{ URL::to('/services/zid/customers') }}">{{ trans('main.customers') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('zid-products'))
                <li><a href="{{ URL::to('/services/zid/products') }}">{{ trans('main.products') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('zid-orders'))
                <li><a href="{{ URL::to('/services/zid/orders') }}">{{ trans('main.orders') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('zid-abandoned-carts'))
                <li><a href="{{ URL::to('/services/zid/abandonedCarts') }}">{{ trans('main.abandonedCarts') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('zid-reports'))
                <li><a href="{{ URL::to('/services/zid/reports') }}">{{ trans('main.notReports') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
                @if(\Helper::checkRules('zid-templates'))
                <li><a href="{{ URL::to('/services/zid/templates') }}">{{ trans('main.templates') }} <i class="flaticon-left-arrow"></i></a></li>
                @endif
            </ul>
        </div>
        @endif
        @if(\Helper::checkRules('whatsapp-orders'))
        <div class="linkStyle">
            <a href="{{ URL::to('/whatsappOrders/orders') }}" class="link">
                <i class="icon flaticon-layer"></i> {{ trans('main.whatsappOrders') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-bots'))
        <div class="linkStyle">
            <a href="{{ URL::to('/bots') }}" class="link">
                <i class="icon flaticon-robot"></i> {{ trans('main.bot') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-templates'))
        <div class="linkStyle">
            <a href="{{ URL::to('/templates') }}" class="link">
                <i class="icon flaticon-layer"></i> {{ trans('main.templates') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-replies'))
        <div class="linkStyle">
            <a href="{{ URL::to('/replies') }}" class="link">
                <i class="icon flaticon-chat-bubble"></i> {{ trans('main.replies') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-categories'))
        <div class="linkStyle">
            <a href="{{ URL::to('/categories') }}" class="link">
                <i class="icon flaticon-menu"></i> {{ trans('main.categories') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-bots-plus'))
        <div class="linkStyle">
            <a href="{{ URL::to('/botPlus') }}" class="link">
                <i class="icon flaticon-robot"></i> {{ trans('main.botPlus') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-contacts'))
        <div class="linkStyle">
            <a href="{{ URL::to('/contacts') }}" class="link">
                <i class="icon flaticon-add-group"></i> {{ trans('main.contacts') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-group-messages'))
        <div class="linkStyle">
            <a href="{{ URL::to('/groupMsgs') }}" class="link">
                <i class="icon flaticon-edit"></i> {{ trans('main.groupMsgs') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-group-numbers,add-number-to-group'))
        <div class="linkStyle">
            <a href="{{ URL::to('/groupNumbers') }}" class="link">
                <i class="icon flaticon-add-group"></i> {{ trans('main.groupNumbers') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-statuses'))
        <div class="linkStyle">
            <a href="{{ URL::to('/statuses') }}" class="link">
                <i class="icon flaticon-mode"></i> {{ trans('main.statuses') }}
            </a>
        </div>
        @endif
        {{-- @if(\Helper::checkRules('list-messages-archive'))
        <div class="linkStyle">
            <a href="{{ URL::to('/msgsArchive') }}" class="link">
                <i class="icon flaticon-inbox"></i> {{ trans('main.groupMsgsArc') }}
            </a>
        </div>
        @endif --}}
        @if(\Helper::checkRules('list-tickets'))
        <div class="linkStyle">
            <a href="{{ URL::to('/tickets') }}" class="link">
                <i class="icon flaticon-tag"></i> {{ trans('main.tickets') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-storage'))
        <div class="linkStyle">
            <a href="{{ URL::to('/storage') }}" class="link">
                <i class="icon flaticon-folder"></i> {{ trans('main.storage') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-invoices'))
        <div class="linkStyle">
            <a href="{{ URL::to('/invoices') }}" class="link">
                <i class="icon flaticon-invoice"></i> {{ trans('main.invoices') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('list-users,list-groups'))
        <div class="linkStyle">
            <a href="{{ URL::to('/users') }}" class="link">
                <i class="icon flaticon-group"></i> {{ trans('main.users') }}
            </a>
        </div>
        @endif
        @if(\Helper::checkRules('apiSetting,apiGuide,webhookSetting'))
        <div class="linkStyle">
            <a href="{{ URL::to('/profile/apiSetting') }}" class="link">
                <i class="icon flaticon-settings"></i> {{ trans('main.api_setting') }}
            </a>
        </div>
        @endif
        <div class="linkStyle">
            <a href="{{ URL::to('/profile/personalInfo') }}" class="link">
                <i class="icon flaticon-pages"></i> {{ trans('main.account_setting') }}
            </a>
        </div>
        <div class="linkStyle">
            <a href="{{ URL::to('/helpCenter') }}" class="link">
                <i class="icon flaticon-life-buoy"></i> {{ trans('main.helpCenter') }}
            </a>
        </div>
        <div class="linkStyle">
            <a href="{{ URL::to('/logout') }}" class="link">
                <i class="icon flaticon-user"></i> {{ trans('main.logout') }}
            </a>
        </div>
    </div>
</div>

@endsection

{{-- Scripts Section --}}
@section('topScripts')
@endsection