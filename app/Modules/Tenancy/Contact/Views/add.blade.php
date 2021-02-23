{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

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
    </div>     
    <!-- end page title --> 
    <input type="hidden" name="modelProps" value="{{ json_encode($data->modelProps) }}">
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-body" style="padding-top: 0">
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/contacts/create') }}">
                        @csrf
                        <div id="basicwizard">
                            <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                <li class="nav-item">
                                    <a href="#basictab1" data-toggle="tab" class="active nav-link rounded-0 pt-2 pb-2"> 
                                        <i class="mdi mdi-cogs mr-1"></i>
                                        <span class="d-none d-sm-inline">{{ trans('main.mainSettings') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#basictab2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                        <i class="mdi mdi-human-baby-changing-table mr-1"></i>
                                        <span class="d-none d-sm-inline">{{ trans('main.manual') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#basictab3" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                        <i class="mdi mdi-format-list-numbered-rtl mr-1"></i>
                                        <span class="d-none d-sm-inline">{{ trans('main.whatsappNos') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#basictab4" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                        <i class="mdi mdi-microsoft-excel mr-1"></i>
                                        <span class="d-none d-sm-inline">{{ trans('main.excelFile') }}</span>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content b-0 mb-0 pt-0">
                                <div class="tab-pane active" id="basictab1">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row mb-3">
                                                <label class="col-3 col-form-label">{{ trans('main.group') }} :</label>
                                                <div class="col-9">
                                                    <select class="selectpicker" data-style="btn-outline-myPR" name="group_id">
                                                        <option value="">{{ trans('main.choose') }}</option>
                                                        @foreach($data->groups as $group)
                                                        <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->title }}</option>
                                                        @endforeach
                                                        <option value="@">{{ trans('main.add') }}</option>
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="new hidden">
                                                <hr>
                                                <p><i class="fa fa-plus"></i> {{ trans('main.add').' '.trans('main.group') }}</p>
                                                <div class="form-group row mb-3">
                                                    <label class="col-3 col-form-label">{{ trans('main.channel') }} :</label>
                                                    <div class="col-9">
                                                        <select class="selectpicker channel" data-style="btn-outline-myPR">
                                                            <option value="">{{ trans('main.channel') }}</option>
                                                            @foreach($data->channels as $channel)
                                                            <option value="{{ $channel }}" {{ old('channel') == $channel || (Session::has('channel') && Session::get('channel') == $channel) ? 'selected' : '' }}>{{ $channel }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> 
                                                <div class="form-group row mb-3">
                                                    <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.titleAr') }} :</label>
                                                    <div class="col-9">
                                                        <input type="text" class="form-control name_ar" placeholder="{{ trans('main.titleAr') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-3">
                                                    <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.titleEn') }} :</label>
                                                    <div class="col-9">
                                                        <input type="text" class="form-control name_en" placeholder="{{ trans('main.titleEn') }}">
                                                        <input type="hidden" name="status">
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-success mb-2 addGR float-right">{{ trans('main.add').' '.trans('main.group') }}</button>

                                            </div>
                                        </div> <!-- end col -->
                                    </div> <!-- end row -->
                                </div>

                                <div class="tab-pane" id="basictab2">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="form-group row mb-3">
                                                <label class="col-md-3 col-form-label" for="name"> {{ trans('main.name') }}</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="client_name" class="form-control" placeholder="{{ trans('main.name') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <label class="col-md-3 col-form-label" for="email"> {{ trans('main.email') }}</label>
                                                <div class="col-md-9">
                                                    <input type="email" name="email" class="form-control" placeholder="{{ trans('main.email') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <label class="col-md-3 col-form-label" for="whatsappNo"> {{ trans('main.whatsappNo') }}</label>
                                                <div class="col-md-9">
                                                    <input type="tel" name="whatsappNo" class="form-control" placeholder="{{ trans('main.whatsappNo') }}">
                                                </div>
                                            </div>
                                        </div> <!-- end col -->
                                        <div class="col-4">
                                            <div class="form-group row mb-3">
                                                <label class="col-md-3 col-form-label" for="country"> {{ trans('main.country') }}</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="country" class="form-control" placeholder="{{ trans('main.country') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <label class="col-md-3 col-form-label" for="city"> {{ trans('main.city') }}</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="city" class="form-control" placeholder="{{ trans('main.city') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <label class="col-md-3 col-form-label" for="lang"> {{ trans('main.lang') }}</label>
                                                <div class="col-md-9">
                                                    <select class="selectpicker" data-style="btn-outline-myPR" name="lang">
                                                        <option value="">{{ trans('main.choose') }}</option>
                                                        <option value="0">{{ trans('main.arabic') }}</option>
                                                        <option value="1">{{ trans('main.english') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div> <!-- end col -->
                                        <div class="col-12">
                                            <div class="form-group row mb-3">
                                                <label class="col-md-2 col-form-label" for="notes"> {{ trans('main.country') }}</label>
                                                <div class="col-md-10">
                                                    <textarea class="form-control" name="notes" placeholder="{{ trans('main.extraInfo') }}"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end row -->
                                </div>

                                <div class="tab-pane" id="basictab3">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row mb-3">
                                                <label class="col-3 col-form-label">{{ trans('main.whatsappNos') }} :</label>
                                                <div class="col-9">
                                                    <textarea class="form-control" name="whatsappNos" placeholder="{{ trans('main.whatsappNos2') }}"></textarea>
                                                </div>
                                            </div> 
                                        </div> <!-- end col -->
                                    </div> <!-- end row -->
                                </div>

                                <div class="tab-pane" id="basictab4">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row mb-3">
                                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.attachExcel') }} :</label>
                                                <div class="col-9">
                                                    <div class="dropzone kt_dropzone_1">
                                                        <div class="fallback">
                                                            <input name="file" type="file" />
                                                        </div>
                                                        <div class="dz-message needsclick">
                                                            <i class="h1 text-muted dripicons-cloud-upload"></i>
                                                            <h3>{{ trans('main.dropzoneP') }}</h3>
                                                        </div>
                                                    </div>
                                                    <p class="mt-2 example">{{ trans('main.excelExample') }} (<a target="_blank" href="{{ URL::to('/').'/uploads/ImportGroupNumbers.xlsx' }}">{{ trans('main.download') }}</a> )</p>
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
                            </div> <!-- tab-content -->
                        </div> <!-- end #basicwizard-->
                        <input type="hidden" name="status">
                        <input type="hidden" name="vType">
                        <hr>
                        <div class="form-group mb-0 justify-content-end row">
                            <div class="col-9">
                                <button type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                <button type="submit" class="btn btn-primary AddBTN" id="SaveBTN">{{ trans('main.draft') }}</button>
                                <button type="reset" class="btn btn-danger Reset">{{ trans('main.clearAll') }}</button>
                            </div>
                        </div>
                    </form>
                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="fas fa-align-center"></i> {{ trans('main.lastActions') }}</h4>
                        </div>
                    </div>
                    <hr>
                    <div class="timeline" dir="ltr">
                        @foreach($data->timelines as $key => $timeline)
                        <article class="timeline-item {{ $key%2 == 1 ? 'timeline-item-left' : '' }}">
                            <div class="timeline-desk">
                                <div class="timeline-box">
                                    <span class="arrow"></span>
                                    <span class="timeline-icon"><i class="mdi mdi-adjust"></i></span>
                                    <h4 class="mt-0 font-16">{{ $timeline->typeText }}</h4>
                                    <p class="text-muted mb-1"><i class="fa fa-clock"></i> <small>{{ $timeline->created_at2 }}</small></p>
                                    <p class="mb-0"><i class="fa fa-user-tie"></i> {{ $timeline->username }}</p>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
<!-- end row-->
</div> <!-- container -->
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>
<script src="{{ asset('libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
<script src="{{ asset('components/contacts.js') }}" type="text/javascript"></script>
@endsection