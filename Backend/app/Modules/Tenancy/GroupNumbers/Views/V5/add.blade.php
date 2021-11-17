{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('content')
<div class="formNumbers">
    <input type="hidden" name="modelProps" value="{{ json_encode($data->modelProps) }}">
    <div class="row">
        <div class="col-md-6">
            <form class="form" method="post" action="{{ URL::to('/addGroupNumbers/create') }}">
                @csrf
                <input type="hidden" name="status">
                <h2 class="title">{{ $data->designElems['mainData']['title'] }}</h2>
                <div class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="titleLabel hidden-sm hidden-xs">{{ trans('main.group') }}</label>
                            <label class="titleLabel titleUpload  hidden-sm hidden-xs">يمكنك رفع ملفات اكسيل</label>
                        </div>
                        <div class="col-md-8">
                            <label class="titleLabel hidden-lg hidden-md">{{ trans('main.group') }}</label>
                            <div class="selectStyle">
                                <select data-toggle="select2" name="group_id">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @foreach($data->groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->channel .' - '.$group->title }}</option>
                                    @endforeach
                                    <option value="@">{{ trans('main.add') }}</option>
                                </select>
                            </div>
                            <div class="new d-hidden">
                                <hr>
                                <p style="padding: 30px 0"> {{ trans('main.add').' '.trans('main.group') }}</p>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="inputEmail3" class="titleLabel">{{ trans('main.titleAr') }} :</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="name_ar" placeholder="{{ trans('main.titleAr') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="inputEmail4" class="titleLabel">{{ trans('main.titleEn') }} :</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="name_en" placeholder="{{ trans('main.titleEn') }}">
                                        <input type="hidden" name="status">
                                    </div>
                                </div>
                                <div class="nextPrev clearfix">
                                    <button type="button" style="width: 150px" class="btnNext mb-2 addGR float-right">{{ trans('main.add').' '.trans('main.group') }}</button>
                                </div>
                            </div>
                            
                            <label class="titleLabel hidden-lg hidden-md">{{ trans('main.attachExcel') }}</label>
                            <label class="upload">
                                <input type="file" name="file" accept=".xlsx,.csv" />
                                <i class="flaticon-upload"></i>
                                {{ trans('main.dropzoneP') }}
                            </label>
                            <div class="uploadFile">{{ trans('main.excelExample') }} (<a target="_blank" href="{{ URL::to('/').'/public/uploads/ImportGroupNumbers.xlsx' }}">{{ trans('main.download') }}</a> )</div>
                            <div class="nextPrev clearfix">
                                <button class="btnNext" disabled>{{ trans('main.prev') }}</button>
                                <button class="btnNext">{{ trans('main.add') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form class="form">
                <h2 class="title">{{ trans('main.fileContent') }}</h2>
                <div class="row">
                    <div class="sortable-list tasklist list-unstyled col">
                        <div class="" id="colData">
                            <p>{{ trans('main.noDataFound') }}</p>
                        </div>
                    </div>
                </div>            
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>
<script src="{{ asset('V5/components/addNumberToGroup.js') }}" type="text/javascript"></script>
@endsection
