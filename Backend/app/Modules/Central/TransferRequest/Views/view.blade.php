{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('tenancy/assets/css/default-skin.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('tenancy/assets/css/photoswipe.css') }}">
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/update/'.$data->data->id) }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.order_no') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" readonly value="{{ $data->data->order_no }}" name="order_no" id="inputEmail3" placeholder="{{ trans('main.order_no') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.client') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" readonly value="{{ $data->data->client }}" name="client" id="inputPassword3" placeholder="{{ trans('main.client') }}">
                                <input type="hidden" name="status" value="{{ $data->data->status }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.domain') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" readonly value="{{ $data->data->domain }}" name="domain" id="inputPassword3" placeholder="{{ trans('main.domain') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.total') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" readonly value="{{ $data->data->total }}" name="total" id="inputPassword3" placeholder="{{ trans('main.total') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.created_at') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" readonly value="{{ $data->data->created_at }}" name="created_at" id="inputPassword3" placeholder="{{ trans('main.created_at') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="w-100">
                                <p class="tx-bold mt-4">{{ trans('main.invoice_items') }}</p>
                                <div class="table">
                                    <table class="table mt-4 table-centered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th colspan="3">{{ trans('main.item') }}</th>
                                                <th>{{ trans('main.quantity') }}</th>
                                                <th class="text-center">{{ trans('main.total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data->data->items as $key => $item)
                                            <tr class="mainRow">
                                                <td>{{ $key+1 }}</td>
                                                <td colspan="3">
                                                    <p class="m-0 d-inline-block align-middle font-16">
                                                        <a href="#" class="text-reset font-family-secondary">{{ $item['data']['title_'.LANGUAGE_PREF] }}</a><br>
                                                        <small class="mr-2"><b>{{ trans('main.extra_type') }}:</b> {{ trans('main.'.$item['type']) }} </small>
                                                    </p>
                                                </td>
                                                <td>{{ $item['data']['quantity'] }}</td>
                                                <td class="text-center">{{ $item['data']['quantity'] * $item['data']['price_after_vat'] }} {{ trans('main.sar') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive -->
                            </div> <!-- end col -->
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.status') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="status">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ $data->data->status == 1 ? 'selected' : '' }}>{{ trans('main.requestSent') }}</option>
                                    <option value="2" {{ $data->data->status == 2 ? 'selected' : '' }}>{{ trans('main.accept') }}</option>
                                    <option value="3" {{ $data->data->status == 3 ? 'selected' : '' }}>{{ trans('main.refuse') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-3 col-form-label">{{ trans('main.image') }} :</label>
                            <div class="col-9">
                                <div class="dropzone" id="kt_dropzone_11">
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                    <div class="dz-message needsclick">
                                        <i class="h1 si si-cloud-upload"></i>
                                        {{-- <h3>{{ trans('main.dropzoneP') }}</h3> --}}
                                    </div>
                                    @if($data->data->photo != '')
                                    <div class="dz-preview dz-image-preview" id="my-preview">  
                                        <div class="dz-image">
                                            <img alt="image" src="{{ $data->data->photo }}">
                                        </div>  
                                        <div class="dz-details">
                                            <div class="dz-size">
                                                <span><strong>{{ $data->data->photo_size }}</strong></span>
                                            </div>
                                            <div class="dz-filename">
                                                <span data-dz-name="">{{ $data->data->photo_name }}</span>
                                            </div>
                                            <div class="PhotoBTNS">
                                                <div class="my-gallery" itemscope="" itemtype="" data-pswp-uid="1">
                                                   <figure itemprop="associatedMedia" itemscope="" itemtype="">
                                                        <a href="{{ $data->data->photo }}" itemprop="contentUrl" data-size="555x370"><i class="fa fa-search"></i></a>
                                                        <img src="{{ $data->data->photo }}" itemprop="thumbnail" style="display: none;">
                                                    </figure>
                                                </div>              
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0 justify-content-end row">
                            <div class="col-9">
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.edit') }}</button>
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
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


@section('modals')
@include('central.Partials.photoswipe_modal')
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('tenancy/assets/js/photoswipe.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/components/myPhotoSwipe.js') }}"></script>       
@endsection
