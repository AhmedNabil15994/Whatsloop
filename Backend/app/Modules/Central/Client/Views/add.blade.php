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
        <form method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/create') }}">
            @csrf
            <div class="col-lg col-xl">
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
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="settings">
                            <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle mr-1"></i> {{ trans('main.personalInfo') }}</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.name') }}</label>
                                        <input type="text" class="form-control" id="firstname" name="name" placeholder="{{ trans('main.name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname">{{ trans('main.phone') }}</label>
                                        <input type="tel" name="phone" class="form-control teles">
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row -->

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.email') }}</label>
                                        <input type="email" class="form-control" name="email" placeholder="{{ trans('main.email') }}">
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
                                        <input type="text" class="form-control" name="company" placeholder="{{ trans('main.company_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cwebsite">{{ trans('main.domain') }}</label>
                                        <input type="text" class="form-control" name="domain" placeholder="{{ trans('main.domain') }}">
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                            <div class="text-right">
                                <button class="btn btn-success AddBTN SaveBTNs">{{ trans('main.save') }}</button>
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
                                            <option value="{{ $membership->id }}" data-area="{{ $membership->monthly_after_vat }}" data-cols="{{ $membership->annual_after_vat }}">{{ $membership->title }}</option>
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
                                            <option value="1">{{ trans('main.monthly') }}</option>
                                            <option value="2">{{ trans('main.yearly') }}</option>
                                            <option value="3">{{ trans('main.demo') }}</option>
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
                                        <div class="col-9 row mainCol">
                                            <div class="col-6">
                                                <label class="col-5 col-form-label">{{ trans('monthly') }} :</label>
                                                <div class="col-7" style="margin-top: -8px;">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="monthly{{ $addon->id }}" class="monthly old" type="checkbox" name="addons[{{ $addon->id }}][1]">
                                                        <label for="monthly{{ $addon->id }}"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="col-5 col-form-label">{{ trans('yearly') }} :</label>
                                                <div class="col-7" style="margin-top: -8px;">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="yearly{{ $addon->id }}" class="yearly old" type="checkbox" name="addons[{{ $addon->id }}][2]">
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
                                <button class="btn btn-success AddBTN SaveBTNs">{{ trans('main.save') }}</button>
                                <a href="{{ URL::to('/clients') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                            </div>
                        </div>

                        <div class="tab-pane" id="aboutme">

                            <h5 class="mb-4 text-uppercase"><i class="mdi mdi-briefcase mr-1"></i>{{ trans('main.tax_setting') }}</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.address') }} :</label>
                                        <input class="form-control" name="address" value="" placeholder="{{ trans('main.address') }}">
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.address') }} 2 :</label>
                                        <input class="form-control" name="address2" value="" placeholder="{{ trans('main.address') }} 2">
                                    </div> 
                                </div>    
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.city') }} :</label>
                                        <input class="form-control" name="city" value="" placeholder="{{ trans('main.city') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.region') }} :</label>
                                        <input class="form-control" name="region" value="" placeholder="{{ trans('main.region') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.postal_code') }} :</label>
                                        <input class="form-control" name="postal_code" value="" placeholder="{{ trans('main.postal_code') }}">
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.country') }} :</label>
                                        <input class="form-control" value="" name="country" placeholder="{{ trans('main.country') }}">
                                    </div> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.paymentMethod') }} :</label>
                                        <select name="payment_method" class="form-control">
                                            <option value="">{{ trans('main.choose') }}</option>
                                            <option value="1">{{ trans('main.mada') }}</option>
                                            <option value="2">{{ trans('main.visaMaster') }}</option>
                                            <option value="3">{{ trans('main.bankTransfer') }}</option>
                                        </select>
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.currency') }} :</label>
                                        <select name="currency" class="form-control">
                                            <option value="">{{ trans('main.choose') }}</option>
                                            <option value="1">{{ trans('main.sar') }}</option>
                                            <option value="2">{{ trans('main.usd') }}</option>
                                        </select>
                                    </div> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>{{ trans('main.tax_id') }} :</label>
                                        <input class="form-control" name="tax_id" value="" placeholder="{{ trans('main.tax_id') }}">
                                    </div> 
                                </div>
                            </div>

                            <div class="text-right">
                                <button class="btn btn-success AddBTN SaveBTNs">{{ trans('main.save') }}</button>
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
                                        <input class="form-control" name="pin_code" value="" placeholder="{{ trans('main.pinCode') }}">
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname">{{ trans('main.emergencyNumber') }}</label>
                                        <input type="tel" name="emergency_number" class="form-control teles">
                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.status') }} :</label>
                                        <select name="status" class="form-control">
                                            <option value="">{{ trans('main.choose') }}</option>
                                            <option value="0">{{ trans('main.notActive') }}</option>
                                            <option value="1">{{ trans('main.active') }}</option>
                                        </select>
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('main.twoAuthFactor') }} :</label>
                                        <select name="two_auth" class="form-control">
                                            <option value="">{{ trans('main.choose') }}</option>
                                            <option value="0">{{ trans('main.no') }}</option>
                                            <option value="1">{{ trans('main.yes') }}</option>
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
                                                <input id="checkbox3" type="checkbox" name="offers">
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
                                                <input id="checkbox4" type="checkbox" name="notifications">
                                                <label for="checkbox4"></label>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-success AddBTN SaveBTNs">{{ trans('main.save') }}</button>
                                <a href="{{ URL::to('/clients') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                            </div>
                        </div>
                        <!-- end timeline content-->

                    </div> <!-- end tab-content -->
                </div> <!-- end card-box-->

            </div> <!-- end col -->
        </form>
    </div>
  
</div>

@endsection

@section('scripts')
<script src="{{ asset('tenancy/assets/components/addClient.js') }}" type="text/javascript"></script>
@endsection