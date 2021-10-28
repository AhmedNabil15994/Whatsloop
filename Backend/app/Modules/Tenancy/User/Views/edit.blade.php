{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')

@endsection

{{-- Content --}}

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/update/'.$data->data->id) }}">
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
                        <input type="hidden" name="status" value="{{ $data->data->status }}">
                        @foreach($data->designElems['modelData'] as $propKey => $propValue)
                        @if(in_array($propValue['type'], ['email','text','number','password','tel']))
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ $propValue['label'] }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input class="{{ $propValue['class'] }}" {{ $propValue['specialAttr'] }} type="{{ $propValue['type'] }}" name="{{ $propKey }}" value="{{ $propValue['type'] != 'password' ? $data->data->$propKey : '' }}" placeholder="{{ $propValue['label'] }}"  {{ $propValue['type'] == 'tel' ? "dir=ltr" : '' }}>
                                <span class="m-form__help LastUpdate float-right mt-1 mb-0">{{ trans('main.created_at') }} :  {{ $data->data->created_at }}</span>
                            </div>
                        </div>
                        @endif

                        @if($propValue['type'] == 'textarea')
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ $propValue['label'] }} :</label>
                            </div>
                            <div class="col-md-9">
                                <textarea {{ $propValue['specialAttr'] }} name="{{ $propKey }}" class="{{ $propValue['class'] }}" placeholder="{{ $propValue['label'] }}">{{ old($propKey) }}</textarea>
                                <span class="m-form__help LastUpdate float-right mt-1 mb-0">{{ trans('main.created_at') }} :  {{ $data->data->created_at }}</span>
                            </div>
                        </div>
                        @endif

                        @if($propValue['type'] == 'select')
                            @if($data->designElems['mainData']['url'] == 'users' && $propKey != 'channels')
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ $propValue['label'] }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="{{ $propKey }}">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        @foreach($propValue['options'] as $group)
                                        @php $group = (object) $group; @endphp
                                        <option value="{{ $group->id }}" {{ $data->data->$propKey == $group->id ? 'selected' : '' }}>{{ $group->title }}</option>
                                        @endforeach
                                    </select>
                                    <span class="m-form__help LastUpdate float-right mt-1 mb-0">{{ trans('main.created_at') }} :  {{ $data->data->created_at }}</span>
                                </div>
                            </div> 
                            @elseif($data->designElems['mainData']['url'] == 'users' && $propKey == 'channels')
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ $propValue['label'] }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="{{ $propKey }}">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        @foreach($propValue['options'] as $group)
                                        @php $group = (object) $group; @endphp
                                        <option value="{{ $group->id }}" {{ in_array($group->id,$data->data->channelIDS) ? 'selected' : '' }}>{{ $group->title }}</option>
                                        @endforeach
                                    </select>
                                    <span class="m-form__help LastUpdate float-right mt-1 mb-0">{{ trans('main.created_at') }} :  {{ $data->data->created_at }}</span>
                                </div>
                            </div>
                            @else 
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ $propValue['label'] }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="{{ $propKey }}">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        @foreach($propValue['options'] as $group)
                                        @php $group = (object) $group; @endphp
                                        <option value="{{ $group->id }}" {{ $data->data->$propKey == $group->id ? 'selected' : '' }}>{{ $group->title }}</option>
                                        @endforeach
                                    </select>
                                    <span class="m-form__help LastUpdate float-right mt-1 mb-0">{{ trans('main.created_at') }} :  {{ $data->data->created_at }}</span>
                                </div>
                            </div> 
                            @endif
                        @endif
                        @endforeach

                        @if($propValue['type'] == 'image' && \Helper::checkRules('uploadImage-'.$data->designElems['mainData']['nameOne']))
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ $propValue['label'] }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="dropzone" id="kt_dropzone_11">
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                    <div class="dz-message needsclick">
                                        <i class="h1 si si-cloud-upload"></i>
                                        <h3>{{ trans('main.dropzoneP') }}</h3>
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
                                                @if(\Helper::checkRules('deleteImage-'.$data->designElems['mainData']['nameOne']))
                                                <a class="DeletePhoto" data-area="{{ $data->data->id }}"><i class="fa fa-trash" data-name="{{ $data->data->photo_name }}" data-clname="Photo"></i> </a>
                                                @endif                    
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="nextPrev clearfix ">
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                    <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.edit') }}</button>
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
                                            <div class="col-xs-12 border-0 mb-3">
                                                <div class="card permission">
                                                    <div class="card-header">
                                                        @php 
                                                        $allPerm = (array) $permission;
                                                        @endphp
                                                        <label class="ckbox prem">
                                                            <input type="checkbox" name="allPermission" {{ in_array($allPerm[array_keys($allPerm)[0]]['perm_name'], $data->data->extra_rules) ? 'checked' : '' }}>
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
                                                            <div class="col-md-2 mb-2">
                                                                <label class="ckbox prem">
                                                                    <input type="checkbox" name="permission{{ $onePerm['perm_name'] }}" {{ in_array($one, $data->data->extra_rules) ? 'checked' : '' }}>
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

@section('modals')
@include('tenant.Partials.photoswipe_modal')
@endsection


{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('/js/photoswipe.min.js') }}"></script>
<script src="{{ asset('/js/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('/components/myPhotoSwipe.js') }}"></script>      
@endsection
