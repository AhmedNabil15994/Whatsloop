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
                                        <img class="emoji-img" data-area="1" src="{{ asset('emoji/1.svg') }}" alt="">
                                        <img class="emoji-img" data-area="2" src="{{ asset('emoji/2.svg') }}" alt="">
                                        <img class="emoji-img" data-area="3" src="{{ asset('emoji/3.svg') }}" alt="">
                                        <img class="emoji-img" data-area="4" src="{{ asset('emoji/4.svg') }}" alt="">
                                        <img class="emoji-img" data-area="5" src="{{ asset('emoji/5.svg') }}" alt="">
                                    </div>
                                    <textarea name="reply" class="form-control d-block" placeholder="{{ trans('main.postComment') }}"></textarea>
                                    <button class="btn d-block btn-primary addRate mb-2 mt-2 w-100" data-area="{{ $oneLog->id }}"> <i class="typcn typcn-location-arrow"></i> {{ trans('main.send') }}</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{-- <form class="form-horizontal" method="POST" action="#">
                        @csrf
                        <div class="form-group row mb-3">
                            <textarea name="messageText" class="form-control" placeholder="comment">{{ old('messageText') }}</textarea>
                        </div> 
                        <div class="form-group row mb-3">
                            Emoji
                        </div> 
                        <hr class="mt-5">
                        <div class="form-group justify-content-end row">
                            <div class="col-9">
                                <button class="btn btn-success AddBTN">Comment</button>
                            </div>
                        </div>
                    </form>
 --}}                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div> 
    </div> 
</div>
<!-- Loader -->
<div id="global-loader">
    <img src="{{ asset('img/loader-2.svg') }}" class="loader-img" alt="Loader">
</div>
<!-- /Loader -->

<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="index.html">
            <img src="{{ asset('img/brand/logo.png') }}" class="main-logo logo-color1" alt="logo">
            <img src="{{ asset('img/brand/logo2.png') }}" class="main-logo logo-color2" alt="logo">
            <img src="{{ asset('img/brand/logo3.png') }}" class="main-logo logo-color3" alt="logo">
            <img src="{{ asset('img/brand/logo4.png') }}" class="main-logo logo-color4" alt="logo">
            <img src="{{ asset('img/brand/logo5.png') }}" class="main-logo logo-color5" alt="logo">
            <img src="{{ asset('img/brand/logo6.png') }}" class="main-logo logo-color6" alt="logo">
        </a>
        <a class="desktop-logo logo-dark active" href="{{ URL::to('/dashboard') }}"><img src="{{ asset('images/logo.svg') }}" class="main-logo dark-theme" alt="logo"></a>
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
                    <img src="{{ asset('images/dashboard.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.dashboard') }}</span>
                </a>
            </li>

            @if(\Helper::checkRules('list-livechat'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/livechat')) }}" href="{{ URL::to('/livechat') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/chat.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.livechat') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('salla-customers,salla-products,salla-orders,salla-reports,salla-templates'))
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/salla.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.salla') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ URL::to('/profile/services?type=salla') }}">{{ trans('main.service_tethering') }}</a></li>
                    @if(\Helper::checkRules('salla-customers'))
                    <li><a class="slide-item" href="{{ URL::to('/services/salla/customers') }}">{{ trans('main.customers') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('salla-products'))
                    <li><a class="slide-item" href="{{ URL::to('/services/salla/products') }}">{{ trans('main.products') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('salla-orders'))
                    <li><a class="slide-item" href="{{ URL::to('/services/salla/orders') }}">{{ trans('main.orders') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('salla-reports'))
                    <li><a class="slide-item" href="{{ URL::to('/services/salla/reports') }}">{{ trans('main.notReports') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('salla-templates'))
                    <li><a class="slide-item" href="{{ URL::to('/services/salla/templates') }}">{{ trans('main.templates') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif

            @if(\Helper::checkRules('zid-customers,zid-products,zid-orders,zid-reports,zid-templates'))
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/zid.png') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.zid') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ URL::to('/profile/services?type=zid') }}">{{ trans('main.service_tethering') }}</a></li>
                    @if(\Helper::checkRules('zid-customers'))
                    <li><a class="slide-item" href="{{ URL::to('/services/zid/customers') }}">{{ trans('main.customers') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('zid-products'))
                    <li><a class="slide-item" href="{{ URL::to('/services/zid/products') }}">{{ trans('main.products') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('zid-orders'))
                    <li><a class="slide-item" href="{{ URL::to('/services/zid/orders') }}">{{ trans('main.orders') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('zid-reports'))
                    <li><a class="slide-item" href="{{ URL::to('/services/zid/reports') }}">{{ trans('main.notReports') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('zid-templates'))
                    <li><a class="slide-item" href="{{ URL::to('/services/zid/templates') }}">{{ trans('main.templates') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif

          

            @if(\Helper::checkRules('list-bots'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/bots')) }}" href="{{ URL::to('/bots') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/chatbot.png') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.chatBot') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-templates'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/templates')) }}" href="{{ URL::to('/templates') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/templates.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.templates') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-replies'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/replies')) }}" href="{{ URL::to('/replies') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/quick_reply.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.replies') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-categories'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/categories')) }}" href="{{ URL::to('/categories') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/tags.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.categories') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-group-numbers,add-number-to-group,list-contacts'))
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/contacts.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.contacts') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    @if(\Helper::checkRules('list-contacts'))
                    <li><a class="slide-item" href="{{ URL::to('/contacts') }}">{{ trans('main.contacts') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('list-group-numbers'))
                    <li><a class="slide-item" href="{{ URL::to('/groupNumbers') }}">{{ trans('main.groupNumbers') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('add-number-to-group'))
                    <li><a class="slide-item" href="{{ URL::to('/addGroupNumbers') }}">{{ trans('main.addGroupNumbers') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif

            @if(\Helper::checkRules('list-group-messages'))
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/group_messages.svg') }}" alt="">
                    <span class="side-menu__label">{{ trans('main.groupMsgs') }}</span><i class="angle fe fe-chevron-{{ DIRECTION == 'ltr' ? 'right' : 'left' }}"></i>
                </a>
                <ul class="slide-menu">
                    @if(\Helper::checkRules('list-group-messages'))
                    <li><a class="slide-item" href="{{ URL::to('/groupMsgs') }}">{{ trans('main.groupMsgs') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('list-messages-archive'))
                    <li><a class="slide-item" href="{{ URL::to('/groupMsgs/add') }}">{{ trans('main.sendNewMessage') }}</a></li>
                    @endif
                </ul>
            </li>
            @endif

            @if(\Helper::checkRules('list-statuses'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/statuses')) }}" href="{{ URL::to('/statuses') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/statuses.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.statuses') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-groupNumberRepors'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/groupNumberRepors')) }}" href="{{ URL::to('/groupNumberRepors') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/group_numbers_report.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.groupNumberRepors') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-messages-archive'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/msgsArchive')) }}" href="{{ URL::to('/msgsArchive') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/archive.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.msgsArchive') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-tickets'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/tickets')) }}" href="{{ URL::to('/tickets') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/tickets.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.tickets') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-storage'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/storage*')) }}" href="{{ URL::to('/storage') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/file_manager.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.storage') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-invoices'))
            <li class="slide">
                <a class="side-menu__item {{ Active(URL::to('/invoices*')) }}" href="{{ URL::to('/invoices') }}">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/invoice.svg') }}" alt="">
                    <span class="side-menu__label"> {{ trans('main.invoices') }} </span>
                </a>
            </li>
            @endif

            @if(\Helper::checkRules('list-users,list-groups'))
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <div class="side-angle1"></div>
                    <div class="side-angle2"></div>
                    <div class="side-arrow"></div>
                    <img src="{{ asset('images/users.svg') }}" alt="">
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

        </ul>

        <div class="app-sidefooter">
            <a class="side-menu__item" href="{{ URL::to('/profile') }}">
                <img src="{{ asset('images/setting.svg') }}" alt="">
                <span class="side-menu__label">{{ trans('main.account_setting') }}</span>
            </a>
            <a class="side-menu__item" href="{{ URL::to('/faq') }}">
                <img src="{{ asset('images/help.svg') }}" alt="">
                <span class="side-menu__label">{{ trans('main.faqs') }}</span>
            </a>
            <a class="side-menu__item" href="{{ URL::to('/logout') }}">
                <img src="{{ asset('images/logout.svg') }}" alt="">
                <span class="side-menu__label">{{ trans('main.logout') }}</span>
            </a>
        </div>
    </div>
</aside>
<!-- main-sidebar -->