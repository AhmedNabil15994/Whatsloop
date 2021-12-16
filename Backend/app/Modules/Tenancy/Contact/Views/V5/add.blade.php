{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<style type="text/css" media="screen">
    .form{
        overflow: unset;
    }
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

</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <input type="hidden" name="modelProps" value="{{ json_encode($data->modelProps) }}">
    <div class="formNumbers">
        <div class="row">
            <div class="col-md-12">
                <form class="form supportForm" method="POST" action="{{ URL::to('/contacts/create') }}">
                    @csrf
                    <ul class="btnsTabs contacts" id="tabs1">
                        <li id="tab1" class="active" data-contact="1">{{ trans('main.mainSettings') }}</li>
                        <li id="tab2" data-contact="2">{{ trans('main.manual') }}</li>
                        <li id="tab3" data-contact="3">{{ trans('main.whatsappNos') }}</li>
                        <li id="tab4" data-contact="4">{{ trans('main.excelFile') }}</li>
                    </ul>
                    <div class="tabs tabs1">
                        <div class="tab tab1">
                            <div class="content">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="titleLabel">{{ trans('main.group') }}</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="selectStyle">
                                            <select data-toggle="select2" name="group_id">
                                                <option value="">{{ trans('main.choose') }}</option>
                                                @foreach($data->groups as $group)
                                                <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->channel .' - '.$group->title }}</option>
                                                @endforeach
                                                <option value="@">{{ trans('main.add') }}</option>
                                            </select>
                                        </div>
                                        <div class="new d-hidden">
                                            <hr>
                                            <p style="padding: 30px 0"> {{ trans('main.add').' '.trans('main.group') }}</p>
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="inputEmail3" class="titleLabel">{{ trans('main.titleAr') }} :</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="name_ar" placeholder="{{ trans('main.titleAr') }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="inputEmail4" class="titleLabel">{{ trans('main.titleEn') }} :</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="name_en" placeholder="{{ trans('main.titleEn') }}">
                                                    <input type="hidden" name="status">
                                                </div>
                                            </div>
                                            <div class="nextPrev clearfix">
                                                <button type="button" style="width: 150px" class="btnNext mb-2 addGR float-right">{{ trans('main.add').' '.trans('main.group') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab tab2">
                            <div class="content">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.name') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" name="client_name" placeholder="{{ trans('main.name') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.email') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="email" name="email" placeholder="{{ trans('main.email') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.phone') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="tel" name="phone" class="teles" placeholder="{{ trans('main.phone') }}">
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.country') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" name="country" placeholder="{{ trans('main.country') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel"> {{ trans('main.city') }}</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" name="city" placeholder="{{ trans('main.city') }}">
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
                                                        <option value="0">{{ trans('main.arabic') }}</option>
                                                        <option value="1">{{ trans('main.english') }}</option>
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
                                                <textarea name="notes" placeholder="{{ trans('main.extraInfo') }}"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end row -->
                            </div>
                        </div>
                        <div class="tab tab3">
                            <div class="content">
                                 <div class="row">
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel">{{ trans('main.whatsappNos') }} :</label>
                                            </div>
                                            <div class="col-md-9">
                                                <textarea name="whatsappNos" placeholder="{{ trans('main.whatsappNos2') }}"></textarea>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->
                            </div>
                        </div>
                        <div class="tab tab4">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="titleLabel">{{ trans('main.attachExcel') }} :</label>
                                            </div>
                                            <div class="col-md-9">
                                                <label class="titleLabel hidden-lg hidden-md">{{ trans('main.attachExcel') }}</label>
                                                <label class="upload">
                                                    <input type="file" name="file" accept=".xlsx,.csv" />
                                                    <i class="flaticon-upload"></i>
                                                    {{ trans('main.dropzoneP') }}
                                                </label>
                                                <div class="uploadFile">{{ trans('main.excelExample') }} (<a target="_blank" href="{{ URL::to('/').'/uploads/ImportGroupNumbers.xlsx' }}">{{ trans('main.download') }}</a> )</div>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="sortable-list tasklist list-unstyled col">
                                                <div class="row" id="colData">
                                                    {{-- <p>{{ trans('main.noDataFound') }}</p> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="status">
                    <input type="hidden" name="vType">
                    <div class="row">
                        <div class="col-xs-12 text-right actions">
                            <div class="nextPrev clearfix ">
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                <button name="Submit" type="button" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- container -->
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>
<script src="{{ asset('V5/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
<script src="{{ asset('V5/components/contacts.js') }}" type="text/javascript"></script>
@endsection
