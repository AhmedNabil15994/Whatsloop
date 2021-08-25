@extends('tenant.Layouts.master')
@section('title',trans('main.dashboard'))
@section('styles')

@endsection


{{-- Content --}}
@section('content')

@if(isset($data->qrImage))
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="header-title text-danger"><a href="{{ URL::to('/QR') }}" style="color: inherit;">{{ trans('main.gotQrCode') }}</a></h5>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
@endif

<!-- row -->
<div class="row row-sm">
    @if(\Helper::checkRules('list-livechat'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/livechat') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/chat.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.livechat') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-salla-customers'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('profile/services?type=salla') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/salla.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.salla') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-zid-customers'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('profile/services?type=zid') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/zid.png') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.zid') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-bots'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/bots') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/chatbot.png') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.chatBot') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-templates'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/templates') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/templates.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.templates') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-replies'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/replies') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/quick_reply.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.replies') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-categories'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/categories') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/tags.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.categories') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-group-numbers,add-number-to-group,list-contacts'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/contacts') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/contacts.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.contacts') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-group-messages'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/groupMsgs') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/group_messages.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.groupMsgs') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-statuses'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/statuses') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/statuses.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.statuses') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-groupNumberRepors'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/groupNumberRepors') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/group_numbers_report.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.groupNumberRepors') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-messages-archive'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/msgsArchive') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/archive.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.msgsArchive') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-tickets'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/tickets') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/tickets.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.tickets') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-storage'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/storage') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/file_manager.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.storage') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-invoices'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/invoices') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/invoice.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.invoices') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('list-users,list-groups'))
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/users') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/users.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.users') }}</h6>
                </div>
            </a>
        </div>
    </div>
    @endif
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/faq') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/help.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.faqs') }}</h6>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/profile') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/setting.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.account_setting') }}</h6>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
        <div class="card text-center">
            <a href="{{ URL::to('/logout') }}" title="">
                <div class="card-body ">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <img src="{{ asset('images/logout.svg') }}" alt="">
                    </div>
                    <h6 class="mb-1 text-muted">{{ trans('main.logout') }}</h6>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- /row -->


@endsection

{{-- Scripts Section --}}
@section('topScripts')
@endsection
