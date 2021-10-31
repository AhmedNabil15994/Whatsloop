{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')

@endsection

{{-- Content --}}

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <form class="" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/create') }}">
        @csrf
        <div class="row">
            <div class="col-xs-12">
                <div class="form">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <div class="formPayment">    
                        <input type="hidden" name="status">
                        @foreach($data->designElems['modelData'] as $propKey => $propValue)
                        @if(in_array($propValue['type'], ['email','text','number','password','tel']))
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ $propValue['label'] }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="inputStyle {{ $propValue['type'] == 'tel' ? 'telStyle' : '' }}">
                                    <input class="{{ $propValue['class'] }}" {{ $propValue['specialAttr'] }} type="{{ $propValue['type'] }}" name="{{ $propKey }}" value="{{ old($propKey) }}" placeholder="{{ $propValue['label'] }}" {{ $propValue['type'] == 'tel' ? "dir=ltr" : '' }}>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($propValue['type'] == 'textarea')
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ $propValue['label'] }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="inputStyle">
                                    <textarea {{ $propValue['specialAttr'] }} name="{{ $propKey }}" class="{{ $propValue['class'] }}" placeholder="{{ $propValue['label'] }}">{{ old($propKey) }}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($propValue['type'] == 'select')
                        {{-- {{ dd($propValue['options']) }} --}}

                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ $propValue['label'] }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" name="{{ $propKey }}">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        @foreach($propValue['options'] as $group)
                                        @php $group = (object) $group; @endphp
                                        <option value="{{ $group->id }}" {{ old($propKey) == $group->id ? 'selected' : '' }} {{ Session::has($propKey) && Session::get($propKey) == $group->id ? 'selected' : '' }}>{{ $group->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> 
                        @endif

                        @endforeach

                        @if($propValue['type'] == 'image' && \Helper::checkRules('uploadImage-'.$data->designElems['mainData']['nameOne']))
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ $propValue['label'] }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="dropzone" id="kt_dropzone_1">
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                    <div class="dz-message needsclick">
                                        <i class="h1 si si-cloud-upload"></i>
                                        <h3>{{ trans('main.dropzoneP') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

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
        @if($data->designElems['mainData']['url'] == 'users')
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
                                                                    <input type="checkbox" name="permission{{ $onePerm['perm_name'] }}">
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
        @endif
    </form>    
</div>

@endsection

@section('scripts')
<script src="{{ asset('components/phone.js') }}"></script>
@endsection
