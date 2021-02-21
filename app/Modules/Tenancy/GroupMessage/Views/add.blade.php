{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css">
    body{
        overflow-x: hidden;
    }
</style>
@endsection

{{-- Content --}}

@section('content')
<div class="py-2 py-lg-6 subheader-transparent" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h3 class="text-dark font-weight-bold my-1 mr-5 m-subheader__title--separator">{{ $data->designElems['mainData']['title'] }}</h3>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to('/') }}" class="text-muted"><i class="m-nav__link-icon la la-home"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" class="text-muted">{{ $data->designElems['mainData']['title'] }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}" class="text-muted">{{ $data->designElems['mainData']['title'] }}</a>
                    </li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page Heading-->
        </div>
        <!--end::Info-->
        <!--begin::Toolbar-->
        <div class="d-flex align-items-center">
            <!--begin::Dropdown-->
            <div class="main-menu dropdown dropdown-inline">
                <button type="button" class="btn btn-light-primary btn-icon btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ki ki-bold-more-hor"></i>
                </button>
                <div class="dropdown-menu" dropdown-toggle="hover">
                    @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}" class="dropdown-item">
                        <i class="m-nav__link-icon fa fa-plus"></i>
                        <span class="m-nav__link-text">اضافة</span>
                    </a>
                    @endif
                    @if(\Helper::checkRules('sort-'.$data->designElems['mainData']['nameOne']))
                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/arrange') }}" class="dropdown-item">
                        <i class="m-nav__link-icon fa fa-sort-numeric-up"></i>
                        <span class="m-nav__link-text">ترتيب</span>
                    </a>
                    @endif
                    @if(\Helper::checkRules('charts-'.$data->designElems['mainData']['nameOne']))
                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/charts') }}" class="dropdown-item">
                        <i class="m-nav__link-icon flaticon-graph"></i>
                        <span class="m-nav__link-text">الاحصائيات</span>
                    </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <div href="#" class="dropdown-item">
                        <a href="{{ URL::to('/logout') }}" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm">تسجيل الخروج</a>
                    </div>
                </div>
            </div>
            <!--end::Dropdown-->
        </div>
        <!--end::Toolbar-->
    </div>
</div>
<!--begin::Card-->
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="menu-icon {{ $data->designElems['mainData']['icon'] }}"></i>
            </span>
            <h3 class="card-label">{{ $data->designElems['mainData']['title'] }}</h3>
        </div>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane active" id="AddTabs" role="tabpanel">
                <form class="forms m-form m-form--group-seperator-dashed" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/create') }}">  
                    @csrf
                    <div class="form-group m-form__group row mt-15" style="padding-right: 0;padding-left: 0;padding-bottom: 15px;">
                        <div class="col-lg-12">
                            <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">اسم القناة:</label>
                            <select class="form-control select2" name="channel_id">
                                <option value="">حدد اختيارك</option>
                                <option value="1">{{ FULL_NAME }}</option>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group m-form__group row mt-15" style="padding-right: 0;padding-left: 0;padding-bottom: 15px;">
                        <div class="col-lg-12">
                            <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">اسم المجموعة:</label>
                            <select class="form-control select2" name="group_id">
                                <option value="">حدد اختيارك</option>
                                @foreach($data->groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> 
                    <div class="form-group m-form__group row mt-15" style="padding-right: 0;padding-left: 0;padding-bottom: 15px;">
                        <div class="col-lg-12">
                            <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">نوع الرسالة:</label>
                            <select class="form-control select2" name="type">
                                <option value="">حدد اختيارك</option>
                                <option value="1">نص</option>
                                <option value="2">صورة او ملف</option>
                                <option value="3">صوت</option>
                                <option value="4">رابط</option>
                                <option value="5">ارقام واتس اب</option>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group m-form__group row mt-15" style="padding-right: 0;padding-left: 0;padding-bottom: 15px;">
                        <div class="col-lg-12">
                            <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">محتوي الرسالة</label>
                            <textarea name="content" class="form-control">{{ old('content') }}</textarea>
                        </div>
                    </div>
                    {{-- <div class="form-group m-form__group row mt-15" style="padding-right: 0;padding-left: 0;padding-bottom: 15px;">
                        <div class="col-lg-12">
                            <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">{{ $propValue['label'] }}</label>
                            <input class="{{ $propValue['class'] }}" {{ $propValue['specialAttr'] }} type="{{ $propValue['type'] }}" name="{{ $propKey }}" value="{{ old($propKey) }}" maxlength="" placeholder="">
                        </div>
                    </div> --}}

                    {{-- @if($propValue['type'] == 'image')
                    <div class="form-group m-form__group row" style="padding-right: 0;padding-left: 0;padding-bottom: 15px;">
                        <div class="col-lg-12">
                            <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">{{ $propValue['label'] }}</label>
                            <div class="dropzone dropzone-default mb-5" id="kt_dropzone_1">
                                <div class="dropzone-msg dz-message needsclick">
                                    <h3 class="dropzone-msg-title"><i class="flaticon-upload-1 fa-4x"></i></h3>
                                    <span class="dropzone-msg-desc">اسحب الملفات هنا أو انقر هنا للرفع .</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($propValue['type'] == 'textarea')
                    <div class="form-group m-form__group row mt-15" style="padding-right: 0;padding-left: 0;padding-bottom: 15px;">
                        <div class="col-lg-12">
                            <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">{{ $propValue['label'] }}</label>
                            <textarea {{ $propValue['specialAttr'] }} name="{{ $propKey }}" class="{{ $propValue['class'] }}">{{ old($propKey) }}</textarea>
                        </div>
                    </div>
                    @endif --}}
                    {{-- @endforeach --}}
                    <input type="hidden" name="status" value="">
                </form>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-6">
                <input name="Submit" type="submit" class="btn btn-success AddBTN " value="اضافة" id="SubmitBTN">
                <input name="Submit" type="submit" class="btn btn-primary AddBTN " value="حفظ كمسودة" id="SaveBTN">
                <input type="reset" class="btn btn-danger Reset" value="مسح الحقول">
                <input name="Add" type="hidden" value="TRUE" id="SaveBTN">
            </div>
        </div>
    </div>
</div>
<!--end::Card-->
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('/js/pages/crud/forms/editors/summernote.js') }}"></script>
@endsection

