{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

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
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/groups/create') }}">
                        @csrf
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
                        <div class="form-group row mb-3">
                            <label for="inputPassword5" class="col-3 col-form-label">{{ trans('main.permissions') }} :</label>
                            <div class="col-9">
                                <div class="sortable-list tasklist list-unstyled">
                                    <div class="row">
                                        @php $i =0; @endphp
                                        @foreach($data->permissions as $key => $permission)
                                            @if($i % 3 == 0)
                                            </div><div class="row">
                                            @endif
                                        <div class="col-xs-12 col-md-4 border-0 mb-3">
                                            <li>
                                                @foreach($permission as $one => $onePerm)
                                                <div class="checkbox checkbox-blue checkbox-single float-left">
                                                    <input type="checkbox" name="permission{{ $onePerm['perm_name'] }}">
                                                    <label></label>
                                                </div>
                                                <p>{{ $onePerm['perm_title'] }}</p>
                                                <div class="clearfix"></div>
                                                @if(count($permission) > 1 )
                                                <hr>
                                                @endif
                                                @endforeach
                                            </li>
                                        </div>
                                        @php $i++; @endphp
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0 justify-content-end row">
                            <div class="col-9">
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
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