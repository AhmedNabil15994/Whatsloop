@extends('tenant.Layouts.master')
@section('title',trans('main.dashboard'))
@section('styles')

<link href="{{ asset('libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-select-bs4/css//select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

@endsection


{{-- Content --}}
@section('content')

<!-- Start Content-->
<div class="container-fluid">
   <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-md-6 col-xl-6">
                    <div class="card stats">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-2">{{ trans('main.sendStatus') }}</h4>
                                </div>
                                <div class="chart-circle chart-circle-md float-right" data-value="{{ $data->sendStatus == 100 ? 1 : '0.'.$data->sendStatus }}" data-thickness="5" data-color="{{ $data->sendStatus == 100 ? '#00d48f' : '#fa5c7c' }}">
                                    <canvas width="80" height="80"></canvas>
                                    <canvas width="140" height="140"></canvas>
                                    <div class="chart-circle-value">
                                        <div class="tx-20 font-weight-bold">{{ $data->sendStatus }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card stats">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-2">{{ trans('main.serverStatus') }}</h4>
                                </div>
                                <div class="chart-circle chart-circle-md float-right" data-value="{{ $data->serverStatus == 100 ? 1 : '0.'.$data->serverStatus }}" data-thickness="5" data-color="{{ $data->serverStatus == 100 ? '#00d48f' : '#fa5c7c' }}">
                                    <canvas width="80" height="80"></canvas>
                                    <canvas width="140" height="140"></canvas>
                                    <div class="chart-circle-value">
                                        <div class="tx-20 font-weight-bold">{{ $data->serverStatus }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="plan-card text-center">
                                <i class="fas fa-comments plan-icon text-primary"></i>
                                <h6 class="text-drak text-uppercase mt-2">{{ trans('main.messages') }}</h6>
                                <h2 class="mb-2">{{ $data->allMessages }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="plan-card text-center">
                                <i class="fas fa-address-book plan-icon text-primary"></i>
                                <h6 class="text-drak text-uppercase mt-2">{{ trans('main.contacts') }}</h6>
                                <h2 class="mb-2">{{ $data->contactsCount }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="plan-card text-center">
                                <i class="fas fa-share plan-icon text-primary"></i>
                                <h6 class="text-drak text-uppercase mt-2">{{ trans('main.sentMessages') }}</h6>
                                <h2 class="mb-2">{{ $data->sentMessages }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="plan-card text-center">
                                <i class="fas fa-envelope plan-icon text-primary"></i>
                                <h6 class="text-drak text-uppercase mt-2">{{ trans('main.incomeMessages') }}</h6>
                                <h2 class="mb-2">{{ $data->incomingMessages }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="col-sm-12">
                <div class="card overflow-hidden">
                    <div class="card-header mt-3">
                        <h3 class="card-title">{{ trans('main.lastContactsAdded') }}</h3>
                    </div>
                    <div class="card-body lastest">
                        @foreach($data->lastContacts as $contact)
                        <div class="list d-flex align-items-center border-bottom py-3">
                            <div class="avatar brround d-block cover-image" data-image-src="{{ asset('images/def_user.svg') }}" style="background: url('{{ asset('images/def_user.svg') }}') center center;">
                                <span class="avatar-status bg-green"></span>
                            </div>
                            <div class="wrapper w-100 ml-3">
                                <p class="mb-0">
                                    <b dir="ltr">{{ $contact->name }} </b>
                                </p>
                                <div class="d-sm-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-phone text-muted mr-1 ml-1"></i>
                                        <p class="mb-0" dir="ltr">{{ $contact->phone }}</p>
                                    </div>
                                    <small class="text-muted">{{ $contact->created_at2[0] }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0 mt-3"><i class="far fa-message"></i> {{ trans('main.msgsArchive') }}</h3>
                    <p></p>
                </div>
                <div class="card-body lastest-table">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap table-striped nowrap w-100 dataTable">
                            <thead>
                                <tr>
                                    <th class="text-{{ DIRECTION == 'rtl' ? 'left' : 'right' }}">{{ trans('main.dialog') }}</th>
                                    <th class="text-{{ DIRECTION == 'rtl' ? 'left' : 'right' }}">{{ trans('main.messageContent') }}</th>
                                    <th class="text-{{ DIRECTION == 'rtl' ? 'left' : 'right' }}">{{ trans('main.status') }}</th>
                                    <th class="text-center">{{ trans('main.extra_type') }}</th>
                                    <th class="text-{{ DIRECTION == 'rtl' ? 'left' : 'right' }}">{{ trans('main.sentDate') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->data as $message)
                                <tr>
                                    <td class="text-{{ DIRECTION == 'rtl' ? 'left' : 'right' }}">{{ $message->chatId2 }}</td>
                                    <td class="text-{{ DIRECTION == 'rtl' ? 'left' : 'right' }}">
                                        @if($message->whatsAppMessageType == 'chat')
                                        <p class="w-100 d-block mg-0" {{ strlen($message->body) > 200 ? 'style=white-space:pre-line' : '' }} >
                                            {{ $message->body }}
                                        </p>
                                        @else
                                        <p class="w-100 d-block mg-0">{{ trans('main.multimedia') }}</p>
                                        @endif
                                    </td>
                                    <td class="text-{{ DIRECTION == 'rtl' ? 'left' : 'right' }}">{{ $message->sending_status_text }}</td>
                                    <td class="text-center">
                                        @if($message->fromMe)
                                            <i class="fas fa-share" style="color: #00d48f;transform: rotateZ(225deg);"></i>
                                        @else
                                            <i class="fas fa-redo" style="color: #fa5c7c;"></i>
                                        @endif
                                    </td>
                                    <td class="text-{{ DIRECTION == 'rtl' ? 'left' : 'right' }}">{{ $message->created_at_day }}  {{ $message->created_at_time }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('tenant.Partials.pagination')
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card overflow-hidden">
                <div class="card-header mt-3">
                    <h3 class="card-title">{{ trans('main.activityLog') }}</h3>
                </div>
                <div class="card-body lastest">
                    @foreach($data->logs as $log)
                    <div class="list d-flex align-items-center border-bottom py-3">
                        <div class="avatar mr-1 ml-1 brround d-block cover-image" data-image-src="{{ $log->userImage }}" style="background: url('{{ $log->userImage }}') center center;">
                            <span class="avatar-status bg-green"></span>
                        </div>
                        <div class="wrapper w-100 ml-3">
                            <p class="mb-0">
                                <b>{{ $log->user }} </b>
                                {{ $log->typeText }}
                            </p>
                            <div class="d-sm-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="si si-speech text-muted mr-1 ml-1"></i>
                                    <p class="mb-0" dir="ltr">{{ $log->chatId2 }}</p>
                                </div>
                                <small class="text-muted">{{ $log->created_at2 }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->
@endsection

{{-- Scripts Section --}}
@section('topScripts')
@endsection
