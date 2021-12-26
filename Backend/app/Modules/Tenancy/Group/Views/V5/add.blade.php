{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')

@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <form class="form-horizontal" method="POST" action="{{ URL::to('/groups/create') }}">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="form">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <div class="formPayment">                        
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.name_ar') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('name_ar') }}" name="name_ar" id="inputEmail3" placeholder="{{ trans('main.name_ar') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.name_en') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('name_en') }}" name="name_en" id="inputPassword3" placeholder="{{ trans('main.name_en') }}">
                                <input type="hidden" name="status">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="nextPrev clearfix ">
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                    <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <!--end: Datatable-->
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="form">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="title"> {{ trans('main.extraPermissions') }}</h4>
                        </div>
                    </div>
                    <div class="formPayment">
                        <div class="row">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="sortable-list tasklist list-unstyled">
                                        <div class="row">
                                            @foreach($data->permissions as $key => $permission)
                                            <div class="col-xs-12 border-0">
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
                                                            @php $i=0; @endphp
                                                            @foreach($permission as $one => $onePerm)
                                                            @if($i != 0 && $i % 6 == 0 )
                                                                </div><div class="row">
                                                            @endif   
                                                            <div class="col-md-2">
                                                                <label class="ckbox prem">
                                                                    <input type="checkbox" name="permission{{ $onePerm['perm_name'] }}" {{ old('permission'.$onePerm['perm_name'])  == 'on' ? 'checked' : '' }}>
                                                                    <span> {{ $onePerm['perm_title'] }}</span>
                                                                </label>
                                                            </div>
                                                            @php $i++ @endphp
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