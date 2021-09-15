
<div class="switcher-wrapper "> 
    <div class="demo_changer"> 
        <div class="demo-icon"> 
            <a class="bg-primary-transparent active" title="Themes">
                <i class="fa fa-cog fa-spin text-primary"></i>
            </a> 
        </div> 
        <div class="form_holder sidebar-right1"> 
            <div class="card changlogs">
                <div class="card-body pd-0">
                    <div class="row">
                        <h2 class="header-title">{{ trans('main.changeLogs') }}</h2>
                    </div>
                    @php
                        $dataObj = App\Models\Changelog::dataList(1)['data'];
                    @endphp

                    <div class="row">
                        @foreach($dataObj as $logKey => $oneLog)
                        <div class="col logs-col mb-3">
                            <div class="card  pricing-card overflow-hidden">
                                <div class="row bg-{{ $oneLog->color }} text-center">
                                    @if($oneLog->category != '')
                                    <div class="card-status bg-{{ $oneLog->color }}"></div>
                                    <span class="mb-2 cats">{{ $oneLog->category }}</span>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="text-capitalize">
                                        <a href="#">{{ $oneLog->title }}</a>
                                        <small class="text-muted float-right">{{ $oneLog->dateForHuman }}</small>
                                    </h5>
                                    <div class="clearfix"></div>
                                    <div class="text-muted {{ $oneLog->description != '' ? 'mg-b-10' : '' }} desc">{{ $oneLog->description }}</div>
                                </div>
                                <input type="hidden" name="rate" value="">
                                <img class="card-img-bottom" src="{{ $oneLog->photo }}" alt="Changelog Photo">
                                <div class="pt-3 emoji">
                                    <div class="ml-auto mb-3 text-muted text-center">
                                        <img class="emoji-img" data-area="1" src="{{ asset('tenancy/assets/emoji/1.svg') }}" alt="">
                                        <img class="emoji-img" data-area="2" src="{{ asset('tenancy/assets/emoji/2.svg') }}" alt="">
                                        <img class="emoji-img" data-area="3" src="{{ asset('tenancy/assets/emoji/3.svg') }}" alt="">
                                        <img class="emoji-img" data-area="4" src="{{ asset('tenancy/assets/emoji/4.svg') }}" alt="">
                                        <img class="emoji-img" data-area="5" src="{{ asset('tenancy/assets/emoji/5.svg') }}" alt="">
                                    </div>
                                    <textarea name="reply" class="form-control d-block" placeholder="{{ trans('main.postComment') }}"></textarea>
                                    <button class="btn d-block btn-primary addRate mb-2 mt-2 w-100" data-area="{{ $oneLog->id }}"> <i class="typcn typcn-location-arrow"></i> {{ trans('main.send') }}</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div> 
    </div> 
</div>
<!-- Loader -->
<div id="global-loader">
    <img src="{{ asset('tenancy/assets/img/loader-2.svg') }}" class="loader-img" alt="Loader">
</div>
<!-- /Loader -->

<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="index.html">
            <img src="{{ asset('tenancy/assets/img/brand/logo.png') }}" class="main-logo logo-color1" alt="logo">
            <img src="{{ asset('tenancy/assets/img/brand/logo2.png') }}" class="main-logo logo-color2" alt="logo">
            <img src="{{ asset('tenancy/assets/img/brand/logo3.png') }}" class="main-logo logo-color3" alt="logo">
            <img src="{{ asset('tenancy/assets/img/brand/logo4.png') }}" class="main-logo logo-color4" alt="logo">
            <img src="{{ asset('tenancy/assets/img/brand/logo5.png') }}" class="main-logo logo-color5" alt="logo">
            <img src="{{ asset('tenancy/assets/img/brand/logo6.png') }}" class="main-logo logo-color6" alt="logo">
        </a>
        <a class="desktop-logo logo-dark active" href="{{ URL::to('/dashboard') }}"><img src="{{ asset('tenancy/assets/images/logo.svg') }}" class="main-logo dark-theme" alt="logo"></a>
        <div class="app-sidebar__toggle" data-toggle="sidebar">
            <a class="open-toggle" href="#"><i class="header-icon fe fe-chevron-left"></i></a>
            <a class="close-toggle" href="#"><i class="header-icon fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i></a>
        </div>
    </div>
    <div class="main-sidemenu sidebar-scroll">
        <ul class="side-menu">
            {{-- <li><h3>Main</h3></li> --}}
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/dashboard')) }}" href="{{ URL::to('/dashboard') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('tenancy/assets/images/dashboard.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.dashboard') }}</span>
                </a>
            </li>  

            @if(\Helper::checkRules('list-bundles'))
                <li class="slide">
                    <a class="side-menu__item {{ Active(URL::to('/bundles')) }}" href="{{ URL::to('/bundles') }}">
                        <div class="side-angle1"></div>
                        <div class="side-angle2"></div>
                        <div class="side-arrow"></div>
                        <img src="{{ asset('tenancy/assets/images/bill.svg') }}" alt="">
                        <span class="side-menu__label">{{ trans('main.bundles') }}</span>
                    </a>
                </li>  
            @endif

            @if(\Helper::checkRules('list-memberships,list-features'))
               <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('tenancy/assets/images/bill.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.memberships') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    @if(\Helper::checkRules('list-memberships'))
                    <li><a class="slide-item" href="{{ URL::to('/memberships') }}">{{ trans('main.packages') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('list-features'))
                    <li><a class="slide-item" href="{{ URL::to('/features') }}">{{ trans('main.features') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif

            @if(\Helper::checkRules('list-addons,list-extraQuotas'))
               <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('tenancy/assets/images/add.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.addons') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    @if(\Helper::checkRules('list-addons'))
                    <li><a class="slide-item" href="{{ URL::to('/addons') }}">{{ trans('main.addons') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('list-extraQuotas'))
                    <li><a class="slide-item" href="{{ URL::to('/extraQuotas') }}">{{ trans('main.extraQuotas') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif


            @if(\Helper::checkRules('list-clients,list-transfers'))
               <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('tenancy/assets/images/team.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.clients') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    @if(\Helper::checkRules('list-clients'))
                    <li><a class="slide-item" href="{{ URL::to('/clients') }}">{{ trans('main.clients') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('list-transfers'))
                    <li><a class="slide-item" href="{{ URL::to('/transfers') }}">{{ trans('main.transfers') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif

            @if(\Helper::checkRules('list-invoices'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/invoices')) }}" href="{{ URL::to('/invoices') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('tenancy/assets/images/invoice.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.invoices') }}</span>
                </a>
            </li> 
            @endif

            @if(\Helper::checkRules('list-tickets,list-departments'))
               <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('tenancy/assets/images/tickets.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.tickets') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    @if(\Helper::checkRules('list-tickets'))
                    <li><a class="slide-item" href="{{ URL::to('/tickets') }}">{{ trans('main.tickets') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('list-departments'))
                    <li><a class="slide-item" href="{{ URL::to('/departments') }}">{{ trans('main.departments') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif

            @if(\Helper::checkRules('list-users,list-groups'))
               <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('tenancy/assets/images/users.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.users') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    @if(\Helper::checkRules('list-users'))
                    <li><a class="slide-item" href="{{ URL::to('/users') }}">{{ trans('main.users') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('list-groups'))
                    <li><a class="slide-item" href="{{ URL::to('/groups') }}">{{ trans('main.groups') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif
            
            @if(\Helper::checkRules('list-changeLogs,list-categories'))
               <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('tenancy/assets/images/logs.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.changeLogs') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    @if(\Helper::checkRules('list-changeLogs'))
                    <li><a class="slide-item" href="{{ URL::to('/changeLogs') }}">{{ trans('main.changeLogs') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('list-categories'))
                    <li><a class="slide-item" href="{{ URL::to('/categories') }}">{{ trans('main.categorys') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif      
        </ul>

        <div class="app-sidefooter">
            <a class="side-menu__item" href="{{ URL::to('/profile') }}">
                <img src="{{ asset('tenancy/assets/images/setting.svg') }}" alt="">
                <span class="side-menu__label">{{ trans('main.account_setting') }}</span>
            </a>
            <a class="side-menu__item" href="{{ URL::to('/faqs') }}">
                <img src="{{ asset('tenancy/assets/images/help.svg') }}" alt="">
                <span class="side-menu__label">{{ trans('main.faqs') }}</span>
            </a>
            <a class="side-menu__item" href="{{ URL::to('/logout') }}">
                <img src="{{ asset('tenancy/assets/images/logout.svg') }}" alt="">
                <span class="side-menu__label">{{ trans('main.logout') }}</span>
            </a>
        </div>
    </div>
</aside>
<!-- main-sidebar -->