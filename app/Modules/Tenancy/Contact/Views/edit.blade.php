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
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/contacts/update/'.$data->data->id) }}">
                        @csrf
                        <div class="tab-pane" id="basictab2">
                            <div class="row">
                                <div class="col-8">
                                    <div class="form-group row mb-3">
                                        <label class="col-3 col-form-label">{{ trans('main.group') }} :</label>
                                        <div class="col-9">
                                            <select class="selectpicker" data-style="btn-outline-myPR" name="group_id">
                                                <option value="">{{ trans('main.choose') }}</option>
                                                @foreach($data->groups as $group)
                                                <option value="{{ $group->id }}" {{ $data->data->group_id == $group->id ? 'selected' : '' }}>{{ $group->channel . ' - ' .$group->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="form-group row mb-3">
                                        <label class="col-md-3 col-form-label" for="name"> {{ trans('main.name') }}</label>
                                        <div class="col-md-9">
                                            <input type="text" name="name" class="form-control" value="{{ $data->data->name }}" placeholder="{{ trans('main.name') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-md-3 col-form-label" for="email"> {{ trans('main.email') }}</label>
                                        <div class="col-md-9">
                                            <input type="email" name="email" class="form-control" value="{{ $data->data->email }}" placeholder="{{ trans('main.email') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-md-3 col-form-label" for="whatsappNo"> {{ trans('main.whatsappNo') }}</label>
                                        <div class="col-md-9">
                                            <input type="tel" name="phone" class="form-control" value="{{ $data->data->phone2 }}" placeholder="{{ trans('main.whatsappNo') }}">
                                        </div>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-4">
                                    <div class="form-group row mb-3">
                                        <label class="col-md-3 col-form-label" for="country"> {{ trans('main.country') }}</label>
                                        <div class="col-md-9">
                                            <input type="text" name="country" class="form-control" value="{{ $data->data->country }}" placeholder="{{ trans('main.country') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-md-3 col-form-label" for="city"> {{ trans('main.city') }}</label>
                                        <div class="col-md-9">
                                            <input type="text" name="city" value="{{ $data->data->city }}" class="form-control" placeholder="{{ trans('main.city') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-md-3 col-form-label" for="lang"> {{ trans('main.lang') }}</label>
                                        <div class="col-md-9">
                                            <select class="selectpicker" data-style="btn-outline-myPR" name="lang">
                                                <option value="">{{ trans('main.choose') }}</option>
                                                <option value="0" {{ $data->data->lang == 0 ? 'selected' : '' }}>{{ trans('main.arabic') }}</option>
                                                <option value="1" {{ $data->data->lang == 1 ? 'selected' : '' }}>{{ trans('main.english') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-12">
                                    <div class="form-group row mb-3">
                                        <label class="col-md-2 col-form-label" for="notes"> {{ trans('main.country') }}</label>
                                        <div class="col-md-10">
                                            <textarea class="form-control" name="notes" placeholder="{{ trans('main.extraInfo') }}">{{ $data->data->notes }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end row -->
                        </div>
                        <input type="hidden" name="status" value="{{ $data->data->status }}">
                        <hr>
                        <div class="form-group mb-0 justify-content-end row">
                            <div class="col-9">
                                <button type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.edit') }}</button>
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
@endsection