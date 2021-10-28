{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
<style type="text/css" media="screen">
    .form .btnsTabs li{
        width: 200px;
    }
    .form textarea{
        height: 250px;
    }
    .form p.data{
        display: inherit;
    }
    .col-xs-12.text-right.actions .nextPrev{
        padding: 10px 30px 30px 30px;
    }
    .form .content{
        padding-bottom: 0;
    }
    textarea[name="notes"]{
        margin-top: 25px !important;
    }
</style>
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="form">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <form class="supportForm" method="POST" action="{{ URL::to('/contacts/update/'.$data->data->id) }}">
                        @csrf
                        <div class="tab-pane" id="basictab2">
                            <div class="content">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel">{{ trans('main.group') }} :</label>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="selectStyle">
                                                    <select data-toggle="select2" data-style="btn-outline-myPR" name="group_id">
                                                        <option value="">{{ trans('main.choose') }}</option>
                                                        @foreach($data->groups as $group)
                                                        <option value="{{ $group->id }}" {{ $data->data->group_id == $group->id ? 'selected' : '' }}>{{ $group->channel . ' - ' .$group->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.name') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" name="client_name" value="{{ $data->data->name }}" placeholder="{{ trans('main.name') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.email') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="email" name="email" value="{{ $data->data->email }}" placeholder="{{ trans('main.email') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.phone') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="tel" name="phone" class="teles" value="{{ $data->data->phone2 }}" placeholder="{{ trans('main.phone') }}">
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.country') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" name="country" value="{{ $data->data->country }}" placeholder="{{ trans('main.country') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.city') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" name="city" value="{{ $data->data->city }}" placeholder="{{ trans('main.city') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.lang') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="selectStyle">
                                                    <select data-toggle="select2" data-style="btn-outline-myPR" name="lang">
                                                        <option value="">{{ trans('main.choose') }}</option>
                                                        <option value="0" {{ $data->data->lang == 0 ? 'selected' : '' }}>{{ trans('main.arabic') }}</option>
                                                        <option value="1" {{ $data->data->lang == 1 ? 'selected' : '' }}>{{ trans('main.english') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="titleLabel"> {{ trans('main.extraInfo') }}</label>                   
                                            </div>
                                            <div class="col-md-10">
                                                <textarea name="notes" placeholder="{{ trans('main.extraInfo') }}">{{ $data->data->notes }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end row -->
                            </div>
                        </div>
                        <input type="hidden" name="status" value="{{ $data->data->status }}">
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="nextPrev clearfix ">
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                    <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.edit') }}</button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </form>
                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
<!-- end row-->
</div> <!-- container -->
@endsection

@section('scripts')
@endsection
