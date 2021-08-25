{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',trans('main.helpCenter'))

@section('styles')
<link href="{{ asset('libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-select-bs4/css//select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">

    <div class="row">
        <div class="panel panel-primary tabs-style-2">
            <div class=" tab-menu-heading">
                <div class="tabs-menu1">
                    <!-- Tabs -->
                    <ul class="nav panel-tabs main-nav-line">
                        <li><a href="#tab4" class="nav-link {{ Request::has('category_id') ? '' : 'active' }}" data-toggle="tab">{{ trans('main.techSupport') }}</a></li>
                        <li><a href="#tab5" class="nav-link {{ Request::has('category_id') ? 'active' : '' }}" data-toggle="tab">{{ trans('main.changeLogs') }}</a></li>
                        <li><a href="#tab6" class="nav-link" data-toggle="tab">{{ trans('main.faq_title') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body tabs-menu-body main-content-body-right border">
                <div class="tab-content">
                    <div class="tab-pane {{ Request::has('category_id') ? '' : 'active' }}" id="tab4">
                        <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5"><i class="fa fa-plus"></i> {{ trans('main.add') . ' '. trans('main.ticket') }}</h5>
                                        <form class="form-horizontal" method="POST" action="{{ URL::to('/tickets/create') }}">
                                            @csrf
                                            <div class="form-group row mb-3">
                                                <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.client') }} :</label>
                                                <div class="col-9">
                                                    <select class="form-control" data-toggle="select2" name="user_id">
                                                        <option value="">{{ trans('main.choose') }}</option>
                                                        @foreach($data->clients as $client)
                                                        <option value="{{ $client->id }}" {{ $client->id == old('user_id') || $client->id == USER_ID ? 'selected' : '' }}>{{ '#'.$client->id .' - '. $client->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.department') }} :</label>
                                                <div class="col-9">
                                                    <select class="form-control" data-toggle="select2" name="department_id">
                                                        <option value="">{{ trans('main.choose') }}</option>
                                                        @foreach($data->departments as $department)
                                                        <option value="{{ $department->id }}" {{ $department->id == old('department_id') ? 'selected' : '' }}>{{ $department->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.subject') }} :</label>
                                                <div class="col-9">
                                                    <input type="text" class="form-control" value="{{ old('subject') }}" name="subject" id="inputEmail3" placeholder="{{ trans('main.subject') }}">
                                                    <input type="hidden" name="status" value="">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.messageContent') }} :</label>
                                                <div class="col-9">
                                                    <textarea class="form-control" name="description" placeholder="{{ trans('main.messageContent') }}">{{ old('description') }}</textarea>
                                                </div>
                                            </div>
                                            @if(\Helper::checkRules('uploadImage-ticket'))
                                            <div class="form-group m-form__group row" style="padding-right: 0;padding-left: 0;padding-bottom: 10px;">
                                                <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">{{ trans('main.files') }}:</label>
                                                <div class="col-lg-12">
                                                    <div class="dropzone dropzone-multi" data-url="/tickets/add" id="kt_dropzone_4">
                                                        <div class="dropzone-panel mb-lg-0 mb-2">
                                                            <a class="dropzone-select btn btn-primary  btn-sm">{{ trans('main.attachFiles') }}</a>
                                                            {{-- <a class="dropzone-upload btn btn-success  btn-sm">{{ trans('main.uploadAll') }}</a> --}}
                                                        </div>
                                                        <div class="dropzone-items">
                                                            <div class="dropzone-item" style="display:none">
                                                                <div class="dropzone-file">
                                                                    <div class="dropzone-filename" title="some_image_file_name.jpg">
                                                                        <span data-dz-name=""></span>
                                                                        <strong>(
                                                                        <span data-dz-size=""></span>)</strong>
                                                                    </div>
                                                                    <div class="dropzone-error" data-dz-errormessage=""></div>
                                                                </div>
                                                                <div class="dropzone-progress">
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                                                                    </div>
                                                                </div>
                                                                <div class="dropzone-toolbar">
                                                                    <span class="dropzone-delete" data-dz-remove="">
                                                                        <i class="fa fa-times"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="form-text text-muted">{{ trans('main.maxFiles') }}</span>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="form-group mb-0 justify-content-end row">
                                                <div class="col-12">
                                                    <button name="Submit" type="submit" class="btn btn-success d-block w-100 AddBTN" id="SubmitBTN"><i class="fa fa-plus"></i> {{ trans('main.add') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-5"><i class="ti-headphone-alt"></i> {{ trans('main.supportInfo') }}</h5>
                                        <div class="mt-2 user-info btn-list">
                                            {{-- <a class="btn btn-outline-light btn-block" href="">
                                                <i class="typcn typcn-mail mr-2 tx-22 lh-1 float-left"></i>
                                                <span class="float-right">{{ $data->email }}</span>
                                            </a> --}}
                                            <a class="btn btn-outline-light btn-block" href="">
                                                <span class="float-left">
                                                    <i class="typcn typcn-key-outline mr-2 tx-22 lh-1"></i> {{ trans('main.pinCode') }}
                                                </span>
                                                <span class="float-right">{{ $data->pin_code }}</span>
                                            </a>
                                            <a class="btn btn-outline-light btn-block" href="">
                                                <i class="typcn typcn-phone mr-2 tx-22 lh-1 float-left"></i>
                                                <span class="float-right">{{ $data->phone }}</span>
                                            </a>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane {{ Request::has('category_id') ? 'active' : '' }}" id="tab5">
                        <div class="row">
                            <div class="col-8 logs-col">
                                @foreach($data->changeLogs as $logKey => $oneLog)
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
                                        <img class="card-img-bottom" src="{{ $oneLog->photo }}" alt="Changelog Photo">
                                        <div class="pt-3 emoji mt-3">
                                            <div class="ml-auto imgs mb-3 text-muted text-center">
                                                <img class="emoji-img" data-area="1" src="{{ asset('emoji/1.svg') }}" alt="">
                                                <img class="emoji-img" data-area="2" src="{{ asset('emoji/2.svg') }}" alt="">
                                                <img class="emoji-img" data-area="3" src="{{ asset('emoji/3.svg') }}" alt="">
                                                <img class="emoji-img" data-area="4" src="{{ asset('emoji/4.svg') }}" alt="">
                                                <img class="emoji-img" data-area="5" src="{{ asset('emoji/5.svg') }}" alt="">
                                            </div>
                                            <textarea name="reply" class="form-control d-block" placeholder="{{ trans('main.postComment') }}"></textarea>
                                            <input type="hidden" name="rate" value="">
                                            <button class="btn addRate d-block btn-primary mb-2 mt-2 w-100" data-area="{{ $oneLog->id }}"> <i class="typcn typcn-location-arrow"></i> {{ trans('main.send') }}</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div><!-- end col-->
                            <div class="col-4">
                                <div class="col logs-col mb-3">
                                    <div class="card  pricing-card overflow-hidden">
                                        <div class="row bg-primary text-center">
                                            <div class="card-status bg-primary"></div>
                                            <span class="mb-2 cats">{{ trans('main.filterByCat') }}</span>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            @foreach($data->categories as $categoryKey => $oneCategory)
                                            <div class="col mb-2">
                                                <label class="ckbox">
                                                    <input type="checkbox" name="category_id" data-area="{{ $oneCategory->id }}" {{ Request::has('category_id') && Request::get('category_id') == $oneCategory->id ? 'checked' : '' }}>
                                                    <span>{{ $oneCategory->title }}</span>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="tab-pane" id="tab6">
                        <!-- row -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div>
                                            <h6 class="card-title mb-1">{{ trans('main.faq_title') }}</h6>
                                            <p class="tx-12 text-muted card-sub-title">{{ trans('main.faq_p') }}</p>
                                        </div>
                                        <div aria-multiselectable="true" class="accordion" id="accordion" role="tablist">
                                            @foreach($data->data as $key => $one)
                                            <div class="card mb-0">
                                                <div class="card-header" id="headingOne{{ $key }}" role="tab">
                                                    <a aria-controls="collapseOne{{ $key }}" aria-expanded="true" data-toggle="collapse" href="#collapseOne{{ $key }}">{{ $one->title }}</a>
                                                </div>
                                                <div aria-labelledby="headingOne{{ $key }}" class="collapse {{ $key == 0 ? 'show' : '' }}" data-parent="#accordion" id="collapseOne{{ $key }}" role="tabpanel">
                                                    <div class="card-body pd-2">
                                                        <p class="tx-18 text-muted card-sub-title">{{ $one->description }}</p>
                                                        @if($one->photo != '')
                                                        @if($one->type == 'photo')
                                                        <img src="{{ $one->photo }}" alt="">
                                                        @else
                                                        <video width="320" height="240" controls>
                                                            <source src="{{ $one->photo }}" type="video/mp4">
                                                        </video>
                                                        @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- accordion -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- row closed -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('modals')
@include('tenant.Partials.search_modal')
@endsection

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
<script src="{{ asset('libs/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('libs/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('js/pages/crud/datatables/advanced/colvis.min.js') }}"></script>
<script src="{{ asset('components/datatables.js')}}"></script>           
@endsection
