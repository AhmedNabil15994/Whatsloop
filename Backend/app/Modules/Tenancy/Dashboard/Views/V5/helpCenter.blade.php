{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',trans('main.helpCenter'))

@section('styles')
<style type="text/css" media="screen">
    .formNumbers .form.supportForm .content{
        background-color: #fff;
    }
    .form{
        background-color: unset;
    }
    .supportForm.form{
        margin-top: 100px;
    }
    video{
        width: 100%;
        height: 400px;
    }
</style>
@endsection

@section('content')
<div class="formNumbers">
    <div class="form main supportForm">
        <ul class="btnsTabs" id="tabs1">
            <li id="tab1" class="active">{{ trans('main.techSupport') }}</li>
            <li id="tab2" class="hidden">{{ trans('main.changeLogs') }}</li>
            <li id="tab3" class="hidden">{{ trans('main.faq_title') }}</li>
        </ul>
        <div class="tabs tabs1">
            <div class="tab tab1 active">
                <div class="row">
                    <div class="col-md-6">
                        <div class="content">
                            <h2 class="addTitle"><i class="fa fa-plus"></i> {{ trans('main.add') . ' '. trans('main.ticket') }}</h2>
                            <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ URL::to('/tickets/create') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="titleLabel">{{ trans('main.client') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="selectStyle">
                                            <select data-toggle="select2" name="user_id">
                                                <option value="">{{ trans('main.choose') }}</option>
                                                @foreach($data->clients as $client)
                                                <option value="{{ $client->id }}" {{ $client->id == old('user_id') || $client->id == USER_ID ? 'selected' : '' }}>{{ '#'.$client->id .' - '. $client->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="titleLabel">{{ trans('main.department') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="selectStyle">
                                            <select data-toggle="select2" name="department_id">
                                                <option value="">{{ trans('main.choose') }}</option>
                                                @foreach($data->departments as $department)
                                                <option value="{{ $department->id }}" {{ $department->id == old('department_id') ? 'selected' : '' }}>{{ $department->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="titleLabel">{{ trans('main.subject') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" value="{{ old('subject') }}" name="subject" id="inputEmail3" placeholder="{{ trans('main.subject') }}" />
                                        <input type="hidden" name="status" value="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="titleLabel">{{ trans('main.messageContent') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <textarea  name="description" placeholder="{{ trans('main.messageContent') }}">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                                @if(\Helper::checkRules('uploadImage-ticket'))
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="titleLabel">{{ trans('main.files') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="upload">
                                            <input type="file" name="files" />
                                            <i class="flaticon-upload"></i>
                                            {{ trans('main.attachFiles') }}
                                        </label>
                                        <span class="maxSize">{{ trans('main.maxFiles') }}</span>
                                        
                                        <div class="nextPrev clearfix">
                                            <button type="submit" name="Submit" class="btnNext AddBTN">{{ trans('main.add') }}</button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="content">
                            <h2 class="title">{{ trans('main.supportInfo') }}</h2>
                            <div class="codeSupport">
                                <span>{{ trans('main.pinCode') }}
                                    <i>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18.158" height="20.003" viewBox="0 0 18.158 20.003">
                                          <g id="keyword_1_" data-name="keyword (1)" transform="translate(-23.604 0)">
                                            <g id="Group_1539" data-name="Group 1539" transform="translate(33.382 2.61)">
                                              <g id="Group_1538" data-name="Group 1538" transform="translate(0)">
                                                <path id="Path_1131" data-name="Path 1131" d="M279.075,69.233l-1.847-1.847a1.958,1.958,0,0,0-2.771,0h0a1.958,1.958,0,0,0,0,2.771L276.3,72a1.959,1.959,0,1,0,2.771-2.771Zm-.924,1.847c-.013.013-.027.025-.041.037a.653.653,0,0,1-.883-.037l-1.847-1.847a.654.654,0,0,1,0-.924h0a.654.654,0,0,1,.924,0l1.847,1.847A.654.654,0,0,1,278.151,71.081Z" transform="translate(-273.881 -66.811)" fill="#777"/>
                                              </g>
                                            </g>
                                            <g id="Group_1541" data-name="Group 1541" transform="translate(23.604 0)">
                                              <g id="Group_1540" data-name="Group 1540" transform="translate(0 0)">
                                                <path id="Path_1132" data-name="Path 1132" d="M40.422,3.184,38.575,1.337a4.572,4.572,0,0,0-6.9,5.971L23.8,15.193a.653.653,0,0,0,0,.924l1.847,1.847a.653.653,0,0,0,.924,0l.462-.462,2.309,2.309a.653.653,0,0,0,.924,0l1.847-1.847h0l1.847-1.847a.653.653,0,0,0,0-.924l-2.309-2.309,2.8-2.8a4.573,4.573,0,0,0,5.972-6.9Zm-7.852,12.47-.924.924-.462-.462a.653.653,0,0,0-.924.924l.462.462-.924.924-1.847-1.847,2.771-2.771ZM39.5,8.727a3.264,3.264,0,0,1-4.619,0,.653.653,0,0,0-.924,0L26.1,16.578l-.924-.924L33.033,7.8a.653.653,0,0,0,0-.924,3.266,3.266,0,0,1,4.619-4.619L39.5,4.108A3.264,3.264,0,0,1,39.5,8.727Z" transform="translate(-23.604 0)" fill="#777"/>
                                              </g>
                                            </g>
                                          </g>
                                        </svg>

                                    </i></span>
                                <input type="text" value="{{ $data->pin_code }}" />
                            </div>
                            <div class="codeSupport phoneSupport">
                                <i class="flaticon-phone-call"></i>
                                <input type="text" value="{{ $data->phone }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab tab2 hidden">
                <div class="card changLogs">
                    <div class="row">
                        <div class="col-md-8 logs-col">
                            <div class="content">
                                @foreach($data->changeLogs as $logKey => $oneLog)

                                <div class="helpCenter">
                                    @if($oneLog->category != '')
                                    <h2 class="titleHelp float-left">{{ $oneLog->category }}</h2>
                                    @endif
                                    <span class="subTitle float-right">{{ $oneLog->dateForHuman }}</span>
                                    <div class="clearfix"></div>
                                    <div class="accordion {{ $logKey == 0 ? 'active' : '' }}" id="accordion">
                                        <div class="contentStyle {{ $logKey == 0 ? 'active' : '' }}">
                                            <h2 class="contentTitle accordion-title" id="accordion-title">{{ $oneLog->title }}</h2>
                                            <div class="details accordion-content" id="accordion-content">
                                                <p class="mb-3">{{ $oneLog->description }}</p>
                                                <img src="{{ $oneLog->photo }}" />
                                            </div>
                                        </div>
                                        <div class="emoji emojs mt-3">
                                            <div class="imgs text-center">
                                                <img class="emoji-img" data-area="1" src="{{ asset('emoji/1.svg') }}" alt="">
                                                <img class="emoji-img" data-area="2" src="{{ asset('emoji/2.svg') }}" alt="">
                                                <img class="emoji-img" data-area="3" src="{{ asset('emoji/3.svg') }}" alt="">
                                                <img class="emoji-img" data-area="4" src="{{ asset('emoji/4.svg') }}" alt="">
                                                <img class="emoji-img" data-area="5" src="{{ asset('emoji/5.svg') }}" alt="">
                                            </div>
                                        </div>
                                        <div class="ticketContent">
                                            <form class="desc addComment">
                                                <textarea name="reply" placeholder="{{ trans('main.postComment') }}"></textarea>
                                                <input type="hidden" name="rate" value="">
                                                <div class="clearfix">
                                                    <button class="btnStyle addRate" data-area="{{ $oneLog->id }}">{{ trans('main.send') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <hr class="mb-3">
                                @endforeach
                            </div>
                        </div><!-- end col-->
                        <div class="col-md-4">
                            <div class="content">
                                <form class="searchCategory">
                                    <h2 class="titleSearch">{{ trans('main.filterByCat') }}</h2>
                                    <div class="filter">
                                        @foreach($data->categories as $categoryKey => $oneCategory)
                                        <label class="checkStyle ckbox">
                                            <input type="checkbox" name="category_id" data-area="{{ $oneCategory->id }}" {{ Request::has('category_id') && Request::get('category_id') == $oneCategory->id ? 'checked' : '' }} />
                                            <i></i>
                                            {{ $oneCategory->title }} 
                                        </label>
                                        @endforeach
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
            <div class="tab tab3 hidden">
                <div class="content">
                    <div class="helpCenter">
                        <h2 class="titleHelp">{{ trans('main.faq_title') }}</h2>
                        <span class="subTitle">{{ trans('main.faq_p') }}</span>
                        @foreach($data->data as $key => $one)
                        <div class="contentStyle accordion" id="accordion">
                            <h2 class="contentTitle accordion-title" id="accordion-title">{{ $one->title }}</h2>
                            <div class="details accordion-content" id="accordion-content">
                                <div class="desc">{{ $one->description }}</div>
                                @if($one->photo != '')
                                @if($one->type == 'photo')
                                <img class="mt-3" src="{{ $one->photo }}" alt="">
                                @else
                                <video class="mt-3" width="320" height="240" controls>
                                    <source src="{{ $one->photo }}" type="video/mp4">
                                </video>
                                @endif
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
@include('tenant.Partials.search_modal')
@endsection

{{-- Scripts Section --}}

@section('scripts')

@endsection
