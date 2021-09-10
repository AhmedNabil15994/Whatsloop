{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <form class="form-horizontal" method="POST" action="{{ URL::to('/groups/create') }}">
        @csrf
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
                        
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.name_ar') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ old('name_ar') }}" name="name_ar" id="inputEmail3" placeholder="{{ trans('main.name_ar') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.name_en') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ old('name_en') }}" name="name_en" id="inputPassword3" placeholder="{{ trans('main.name_en') }}">
                                <input type="hidden" name="status">
                            </div>
                        </div>
                        <div class="form-group justify-content-end row">
                            <div class="col-9 text-right">
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btn-danger Reset float-left">{{ trans('main.back') }}</a>
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                            </div>
                        </div>
                        <!--end: Datatable-->
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="header-title"> {{ trans('main.permissions') }}</h4>
                            </div>
                        </div>
                        <hr class="mb-5">
                        <div class="row">
                            <div class="form-group row mb-3">
                                <div class="col-12">
                                    <div class="sortable-list tasklist list-unstyled">
                                        <div class="row">
                                            @foreach($data->permissions as $key => $permission)
                                            <div class="col-12 border-0 mb-3">
                                                <div class="card permission">
                                                    <div class="card-header">
                                                        <label class="ckbox prem">
                                                            <input type="checkbox" name="allPermission">
                                                            <span class="tx-bold">{{ trans('main.'.lcfirst(str_replace('Controllers','',$key))) }} </span>
                                                        </label>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            @foreach($permission as $one => $onePerm)
                                                            <div class="col-2 mb-2">
                                                                <label class="ckbox prem">
                                                                    <input type="checkbox" name="permission{{ $onePerm['perm_name'] }}">
                                                                    <span> {{ $onePerm['perm_title'] }}</span>
                                                                </label>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
        <!-- end row-->
    </form>
</div> <!-- container -->
@endsection