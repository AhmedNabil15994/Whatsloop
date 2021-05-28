{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css">
    body{
        overflow-x: hidden;
    }
    form{
        width: 100%;
    }
    .inbox-item-text{
        white-space: pre;
    }
    .btn.btn-md.waves-effect{
        font-weight: bold;
        color: #FFF;
    }
</style>
@endsection

{{-- Content --}}

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-11">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ $data->designElems['mainData']['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['mainData']['title'] }}</h3>
            </div>
        </div>

        <div class="col-1 text-right">
            <div class="btn-group dropleft mb-3 mt-2">
                <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-cog"></i>
                </button>
                <div class="dropdown-menu">
                    @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}"><i class="fa fa-plus"></i> {{ trans('main.add') }}</a>
                    @endif
                    @if(\Helper::checkRules('sort-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/arrange') }}"><i class="fa fa-sort-numeric-up"></i> {{ trans('main.sort') }}</a>
                    @endif
                    @if(\Helper::checkRules('charts-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/charts') }}"><i class="fas fa-chart-bar"></i> {{ trans('main.charts') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-xl-4">
            <div class="card-box text-center">
                <img src="{{ $data->data->photo }}" class="rounded-circle avatar-lg img-thumbnail"
                    alt="profile-image">

                <h4 class="mb-1">{{ $data->data->name }}</h4>
                <a href="{{ URL::to('/clients/invLogin/'.$data->data->id) }}" target="_blank" class="btn btn-success btn-md waves-effect mb-2 waves-light"><i class="fas fa-sign-in-alt"></i> {{ trans('main.invLogin') }}</a>
                <a class="btn btn-md btn-primary shareDays waves-effect mb-2 waves-light" data-toggle="modal" data-target="#transferDaysModal"> <i class="fa fa-share"></i> {{ trans('main.add_days') }}</a>
                <a href="{{ URL::to('/clients/pinCodeLogin/'.$data->data->id) }}" target="_blank" class="btn btn-info btn-md waves-effect mb-2 waves-light"><i class="fas fa-sign-in-alt"></i> {{ trans('main.pinCodeLogin') }}</a>
                <div class="text-left mt-3">
                    <p class="text-muted mb-2 font-13"><strong>{{ trans('main.name') }} :</strong> <span class="ml-2">{{ $data->data->name }}</span></p>

                    <p class="text-muted mb-2 font-13"><strong>{{ trans('main.phone') }} :</strong><span class="ml-2">{{ $data->data->phone }}</span></p>

                    <p class="text-muted mb-2 font-13"><strong>{{ trans('main.email') }} :</strong> <span class="ml-2 ">{{ $data->data->email }}</span></p>

                    <p class="text-muted mb-2 font-13"><strong>{{ trans('main.company_name') }} :</strong> <span class="ml-2 ">{{ $data->data->company }}</span></p>

                    <p class="text-muted mb-1 font-13"><strong>{{ trans('main.channel') }} :</strong> <span class="ml-2"># {{ $data->data->channels[0]->id }}</span></p>
                    <p class="text-muted mb-1 font-13"><strong>{{ trans('main.subscriptionPeriod') }} :</strong> <span class="ml-2"> {{ $data->data->channels[0]->start_date }} - {{ $data->data->channels[0]->end_date }}</span></p>
                    <p class="text-muted mb-1 font-13"><strong>{{ trans('main.leftDays') }} :</strong> <span class="ml-2"> {{ \App\Models\CentralChannel::getData($data->data->channels[0])->leftDays }} {{ trans('main.day') }}</span> </p>
                </div>
            </div> <!-- end card-box -->

            <div class="card-box">
                <h4 class="header-title mb-3">{{ trans('main.last') . trans('main.messages') }}</h4>

                <div class="inbox-widget" data-simplebar style="max-height: 350px;">
                    @foreach($data->messages as $message)
                    <div class="inbox-item">
                        <div class="inbox-item-img"><img src="{{ asset('tenancy/assets/images/logoOnly.jpg') }}" class="rounded-circle" alt=""></div>
                        <p class="inbox-item-author">{{ str_replace('@c.us', '', $message->chatId) }}</p>
                        <p class="inbox-item-text">{!! $message->body !!}</p>
                        <p class="inbox-item-date">
                            <a href="javascript:(0);" class="btn btn-sm btn-link text-info font-13"> {{ \App\Models\ChatDialog::reformDate($message->time) }} </a>
                        </p>
                    </div>
                    @endforeach
                </div> <!-- end inbox-widget -->

            </div> <!-- end card-box-->

        </div>

        <div class="col-lg-8 col-xl-8">
            <div class="card-box">
                <ul class="nav nav-pills navtab-bg nav-justified">
                    <li class="nav-item">
                        <a href="#settings" data-toggle="tab" aria-expanded="true" class="nav-link active">
                            {{ trans('main.personalInfo') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#prods" data-toggle="tab" aria-expanded="false" class="nav-link">
                            {{ trans('main.products') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#aboutme" data-toggle="tab" aria-expanded="false" class="nav-link">
                            {{ trans('main.tax_setting') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#timeline" data-toggle="tab" aria-expanded="false" class="nav-link">
                            {{ trans('main.settings') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tickets" data-toggle="tab" aria-expanded="false" class="nav-link">
                            {{ trans('main.tickets') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="settings">
                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle mr-1"></i> {{ trans('main.personalInfo') }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.name') }}</label>
                                    <input type="text" class="form-control" value="{{ $data->data->name }}" id="firstname" name="name" placeholder="{{ trans('main.name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">{{ trans('main.phone') }}</label>
                                    <input type="tel" value="{{ $data->data->phone }}" name="phone" class="form-control teles">
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.email') }}</label>
                                    <input type="email" value="{{ $data->data->email }}" class="form-control" name="email" placeholder="{{ trans('main.email') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="userpassword">{{ trans('main.password') }}</label>
                                    <input type="password" class="form-control" name="password" placeholder="{{ trans('main.password') }}">
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->

                        <h5 class="mb-3 text-uppercase bg-light p-2"><i class="mdi mdi-office-building mr-1"></i> {{ trans('main.company_info') }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="companyname">{{ trans('main.company_name') }}</label>
                                    <input type="text" class="form-control" value="{{ $data->data->company }}" name="company" placeholder="{{ trans('main.company_name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cwebsite">{{ trans('main.domain') }}</label>
                                    <input type="text" class="form-control" value="{{ $data->data->domain }}" name="domain" placeholder="{{ trans('main.domain') }}">
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->
                        <div class="text-right">
                            <a href="{{ URL::to('/clients') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                        </div>
                    </div>
                    <!-- end settings content-->

                    <div class="tab-pane" id="prods">
                        <h5 class="mb-4 text-uppercase"><i class="fab fa-product-hunt mr-1"></i>{{ trans('main.products') }}</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.packages') }} :</label>
                                    <select name="membership_id" class="form-control">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        @foreach($data->memberships as $membership)
                                        @if($membership->monthly_price != 0)
                                        <option value="{{ $membership->id }}" {{ $membership->id == $data->data->membership_id ? 'selected' : '' }} data-area="{{ $membership->monthly_after_vat }}" data-cols="{{ $membership->annual_after_vat }}">{{ $membership->title }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.subscriptionPeriod') }} :</label>
                                    <select name="duration_type" class="form-control">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="1" {{ $data->data->duration_type == 1 ? 'selected' : '' }}>{{ trans('main.monthly') }}</option>
                                        <option value="2" {{ $data->data->duration_type == 2 ? 'selected' : '' }}>{{ trans('main.yearly') }}</option>
                                        <option value="3" {{ $data->data->duration_type == 3 ? 'selected' : '' }}>{{ trans('main.demo') }}</option>
                                    </select>
                                </div> 
                            </div>
                        </div>

                        <h5 class="mb-4 text-uppercase"><i class=" fas fa-star mr-1"></i>{{ trans('main.addons') }}</h5>
                        @foreach($data->addons as $key => $addon)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-3 col-form-label">{{ $addon->title }} :</label>
                                        @php
                                            $found = [];
                                            if(in_array($addon->id, $data->data->addons != null ?  unserialize($data->data->addons) : [])){
                                                @$found = $data->userAddons[$addon->id];
                                            }
                                        @endphp
                                        <div class="col-9 row mainCol">
                                            <div class="col-6">
                                                <label class="col-5 col-form-label">{{ trans('monthly') }} :</label>
                                                <div class="col-7" style="margin-top: -8px;">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="monthly{{ $addon->id }}" class="monthly" {{ $found == 1 ? 'checked' : '' }} type="checkbox" name="addons[{{ $addon->id }}][1]">
                                                        <label for="monthly{{ $addon->id }}"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="col-5 col-form-label">{{ trans('yearly') }} :</label>
                                                <div class="col-7" style="margin-top: -8px;">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="yearly{{ $addon->id }}" class="yearly" {{ $found == 2 ? 'checked' : '' }} type="checkbox" name="addons[{{ $addon->id }}][2]">
                                                        <label for="yearly{{ $addon->id }}"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <hr class="mt-3">
                                </div>
                            </div>
                            @endforeach
                        <div class="text-right">
                            <a href="{{ URL::to('/clients') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                        </div>
                    </div>

                    <div class="tab-pane" id="aboutme">

                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-briefcase mr-1"></i>{{ trans('main.tax_setting') }}</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.address') }} :</label>
                                    <input class="form-control" name="address" value="{{ $data->paymentInfo->address }}" placeholder="{{ trans('main.address') }}">
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.address') }} 2 :</label>
                                    <input class="form-control" name="address2" value="{{ $data->paymentInfo->address2 }}" placeholder="{{ trans('main.address') }} 2">
                                </div> 
                            </div>    
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.city') }} :</label>
                                    <input class="form-control" name="city" value="{{ $data->paymentInfo->city }}" placeholder="{{ trans('main.city') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.region') }} :</label>
                                    <input class="form-control" name="region" value="{{ $data->paymentInfo->region }}" placeholder="{{ trans('main.region') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.postal_code') }} :</label>
                                    <input class="form-control" name="postal_code" value="{{ $data->paymentInfo->postal_code }}" placeholder="{{ trans('main.postal_code') }}">
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.country') }} :</label>
                                    <input class="form-control" value="{{ $data->paymentInfo->country }}" name="country" placeholder="{{ trans('main.country') }}">
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.paymentMethod') }} :</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="1" {{ $data->paymentInfo->payment_method == 1 ? 'selected' : '' }}>{{ trans('main.mada') }}</option>
                                        <option value="2" {{ $data->paymentInfo->payment_method == 2 ? 'selected' : '' }}>{{ trans('main.visaMaster') }}</option>
                                        <option value="3" {{ $data->paymentInfo->payment_method == 3 ? 'selected' : '' }}>{{ trans('main.bankTransfer') }}</option>
                                    </select>
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.currency') }} :</label>
                                    <select name="currency" class="form-control">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="1" {{ $data->paymentInfo->currency == 1 ? 'selected' : '' }}>{{ trans('main.sar') }}</option>
                                        <option value="2" {{ $data->paymentInfo->currency == 2 ? 'selected' : '' }}>{{ trans('main.usd') }}</option>
                                    </select>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label>{{ trans('main.tax_id') }} :</label>
                                    <input class="form-control" name="tax_id" value="{{ $data->paymentInfo->tax_id }}" placeholder="{{ trans('main.tax_id') }}">
                                </div> 
                            </div>
                        </div>

                        <div class="text-right">
                            <a href="{{ URL::to('/clients') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                        </div>
                    </div> <!-- end tab-pane -->
                    <!-- end about me section content -->

                    <div class="tab-pane" id="timeline">
                        <h5 class="mb-4 text-uppercase"><i class="fas fa-cogs mr-1"></i>{{ trans('main.settings') }}</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.pinCode') }} :</label>
                                    <input class="form-control" name="pin_code" value="{{ $data->data->pin_code }}" placeholder="{{ trans('main.pinCode') }}">
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">{{ trans('main.emergencyNumber') }}</label>
                                    <input type="tel" name="emergency_number" value="{{ $data->data->emergency_number }}" class="form-control teles">
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.status') }} :</label>
                                    <select name="status" class="form-control">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="0" {{ $data->data->status == 0 ? 'selected' : '' }}>{{ trans('main.notActive') }}</option>
                                        <option value="1" {{ $data->data->status == 1 ? 'selected' : '' }}>{{ trans('main.active') }}</option>
                                    </select>
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ trans('main.twoAuthFactor') }} :</label>
                                    <select name="two_auth" class="form-control">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="0" {{ $data->data->two_auth == 0 ? 'selected' : '' }}>{{ trans('main.no') }}</option>
                                        <option value="1" {{ $data->data->two_auth == 1 ? 'selected' : '' }}>{{ trans('main.yes') }}</option>
                                    </select>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-3 col-form-label">{{ trans('main.offers') }} :</label>
                                    <div class="col-9" style="margin-top: -8px;">
                                        <div class="checkbox checkbox-success">
                                            <input id="checkbox3" type="checkbox" name="offers" {{ $data->data->offers == 1 ? 'checked' : '' }} >
                                            <label for="checkbox3"></label>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-3 col-form-label">{{ trans('main.notifications') }} :</label>
                                    <div class="col-9" style="margin-top: -8px;">
                                        <div class="checkbox checkbox-success">
                                            <input id="checkbox4" type="checkbox" name="notifications" {{ $data->data->notifications == 1 ? 'checked' : '' }} >
                                            <label for="checkbox4"></label>
                                        </div>
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <h5 class="mb-3 text-uppercase bg-light p-2"><i class="mdi mdi-office-building mr-1"></i> {{ trans('main.channel_settings') }}</h5>
                        <form action="{{ URL::to('/clients/view/'.$data->data->id.'/updateSettings') }}" method="get" accept-charset="utf-8">
                            <div class="row">
                                @csrf
                                @foreach($data->settings as $key => $setting)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="companyname">{{ $key }}</label>
                                        <input type="text" class="form-control" value="{{ $setting }}" name="{{ $key }}" placeholder="{{ $key }}">
                                    </div>
                                </div>
                                @endforeach
                            </div> <!-- end row -->
                            <button class="btn btn-success">{{ trans('main.save'). ' '.trans('main.channel_settings') }}</button>
                        </form>
                        <div class="text-right">
                            <a href="{{ URL::to('/clients') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                        </div>
                    </div>
                    <!-- end timeline content-->

                    <div class="tab-pane" id="tickets">
                        <h5 class="mb-4 text-uppercase"><i class=" dripicons-ticket mr-1"></i>{{ trans('main.tickets') }}</h5>
                        
                        @if(!empty($data->tickets))
                        <!-- start user projects -->
                        <table class="data table table-striped no-margin">
                            <thead>
                                <tr>
                                    <th>{{ trans('main.id') }}</th>
                                    <th>{{ trans('main.department') }}</th>
                                    <th>{{ trans('main.subject') }}</th>
                                    <th>{{ trans('main.client') }}</th>
                                    <th>{{ trans('main.priority') }}</th>
                                    <th>{{ trans('main.date') }}</th>
                                    <th>{{ trans('main.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->tickets as $key => $value)
                                <tr>
                                    <td width="3%">{{ $value->id }}</td>
                                    <td>{{ $value->department }}</td>
                                    <td>{{ $value->subject }}</td>
                                    <td>{{ $value->client }}</td>
                                    <td>{{ $value->priority }}</td>
                                    <td>{{ $value->created_at }}</td>
                                    <td width="150px" align="center">
                                        @if(\Helper::checkRules('edit-ticket'))
                                            <a href="{{ URL::to('/tickets/edit/' . $value->id) }}" class="btn btn-success btn-xs"><i class="fa fa-pencil-alt"></i></a>
                                        @endif
                                        @if(\Helper::checkRules('view-ticket'))
                                            <a href="{{ URL::to('/tickets/view/' . $value->id) }}" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- end user projects -->
                        @else
                        <div class="empty">{{ trans('main.noTickets') }}</div>
                        @endif

                        <div class="text-right">
                            <a href="{{ URL::to('/clients') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                        </div>
                    </div>
                </div> <!-- end tab-content -->
            </div> <!-- end card-box-->

            <div class="card-box">
                <div class="row">
                    <div class="col-md-6 col-xl-3">
                        <div class="card-box">
                            <i class="fab fa-rocketchat text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.messages') }}"></i>
                            <h4 class="mt-0 font-16">{{ trans('main.messages') }}</h4>
                            <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->allMessages }}</span></h2>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card-box">
                            <i class="fas fa-reply text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.sentMessages') }}"></i>
                            <h4 class="mt-0 font-16">{{ trans('main.sentMessages') }}</h4>
                            <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->sentMessages }}</span></h2>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card-box">
                            <i class="fas fa-redo text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.incomeMessages') }}"></i>
                            <h4 class="mt-0 font-16">{{ trans('main.incomeMessages') }}</h4>
                            <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->incomingMessages }}</span></h2>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card-box">
                            <i class="fas fa-users text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.contacts') }}"></i>
                            <h4 class="mt-0 font-16">{{ trans('main.contacts') }}</h4>
                            <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->contactsCount }}</span></h2>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-2">
                                        <img src="{{ asset('tenancy/assets/images/logoOnly.jpg') }}" width="100%" alt="">
                                    </div>
                                    <div class="col-10">
                                        <div class="row">
                                            <!--begin::Title-->
                                            <div class="d-flex justify-content-between flex-wrap mt-1">
                                                <div class="d-flex mr-3">
                                                    <a href="#" class="text-dark font-size-h5 font-weight-bold mr-3">{{ $data->channel->name }}</a>
                                                    <a href="#">
                                                        <i class="fas fa-check-circle badge-outline-success"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <!--end::Title-->
                                            <!--begin::Content-->
                                            <div class="d-flex flex-wrap justify-content-between mt-1">
                                                <div class="d-flex flex-column flex-grow-1 pr-8">
                                                    <div class="d-flex flex-wrap mb-4">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                    {{ trans('main.channel') }} : <b># {{ $data->channel->id }}</b>
                                                                </a>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                    {{ trans('main.phone') }} : <b>{{ str_replace('@c.us', '', $data->me->id) }}</b>
                                                                </a>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                    {{ trans('main.connection_date') }}: <b style="direction: ltr;display: inline-block;">{{ $data->status->created_at }}</b>
                                                                </a>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                            {{ trans('main.phone_status') }} : <b><div class="badge badge-lg badge-success badge-inline">{{ $data->status->statusText }}</div></b>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                            {{ trans('main.msgSync') }} : <b><div class="badge badge-lg badge-success badge-inline">{{ $data->allMessages > 0 ? trans('main.synced') : trans('main.notSynced') }}</div></b>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                            {{ trans('main.contSync') }} : <b><div class="badge badge-lg badge-success badge-inline">{{ $data->contactsCount > 0 ? trans('main.synced') : trans('main.notSynced') }}</div></b>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-sm-3">
                                                                        <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                            {{ trans("main.phone_battery") }} : <b>{{ $data->me->battery }}%</b>
                                                                        </a>
                                                                    </div>

                                                                    <div class="col-sm-3">
                                                                        <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                            {{ trans('main.phone_type') }} : <b>{{ $data->me->device['manufacturer'] }}</b>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                            {{ trans('main.phone_model') }} : <b>{{ $data->me->device['model'] }}</b>
                                                                        </a>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                            {{ trans('main.os_ver') }} : <b>{{ $data->me->device['os_version'] }}</b>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center w-25 flex-fill float-right mt-lg-12 mt-8">
                                                    <span class="font-weight-bold text-dark-75">{{ trans('main.leftDays') }}</span>
                                                    <div class="progress progress-xs mx-3 w-100">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $data->channel->rate }}%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="80"></div>
                                                    </div>
                                                    <span class="font-weight-bolder text-dark">{{ $data->channel->leftDays }} {{ trans('main.day') }}</span>
                                                </div>
                                            </div>
                                            <!--end::Content-->
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card body-->
                        </div> <!-- end card -->
                    </div><!-- end col-->
                </div>
            </div>
        </div> <!-- end col -->
    </div>  
</div>

@endsection

@section('modals')
@include('central.Partials.photoswipe_modal')
@include('central.Partials.transferDaysModal')
@endsection


@section('scripts')
<script src="{{ asset('tenancy/assets/js/photoswipe.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/components/myPhotoSwipe.js') }}"></script>      
<script src="{{ asset('tenancy/assets/components/addClient.js') }}" type="text/javascript"></script>
@endsection