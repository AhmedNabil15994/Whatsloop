{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css" media="screen">
    .col-xl-8 .rounded-circle{
        width: 50px;
        height: 50px;
    }
    .col-xl-8 .media .media-body{
        margin-top: 15px;
    }
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <!-- project card -->
            <div class="card d-block ticket">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <h3 class="header-title"><i class="ti-receipt"></i> {{ ucfirst($data->data->subject) }}</h3>
                        </div>
                        <div class="col-6 text-right">
                            <h3 class="header-title">{{ trans('main.ticket') }} #{{ $data->data->id }}</h3>
                        </div>
                    </div>
                    
                    <hr>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        {!! $data->data->description !!}
                    </p>

                </div> <!-- end card-body-->
                
            </div> <!-- end card-->

            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4 mt-0 font-16">{{ trans('main.comments') }} ({{ $data->commentsCount }})</h4>
                    <div class="clerfix"></div>
                    @foreach($data->comments as $comment)
                    <div class="row empCard">
                        <div class="row person {{ $data->data->user_id != $comment->created_by ? 'employee' : 'owner' }}">
                            <div class="col-6">
                                <h4 class="card-title">
                                    <img class="m{{ DIRECTION == 'ltr' ? 'r' : 'l' }}-2 avatar-sm rounded-circle" src="{{ $data->data->user_id != $comment->created_by ? $comment->image  : asset('images/def_user.svg') }}" alt="Generic placeholder image"> 
                                    <span>{{ $comment->creator }}</span>
                                    <span class="d-block">{{ $data->data->user_id != $comment->created_by ? trans('main.employee') : trans('main.owner') }}</span>
                                </h4>
                            </div>
                            <div class="col-6 text-right">
                                <span class="text-muted">{{ $comment->created_at }}</span>
                            </div>
                        </div>
                        <div class="row comment">
                            <p>{!! $comment->comment !!}</p>
                        </div>
                    </div>
                    @endforeach
                </div> <!-- end card-body-->
            </div>
            <!-- end card-->

            <div class="card">
                <div class="card-header">
                    <h4 class="header-title"><i class="ti-plus"></i> {{ trans('main.addComment') }}</h4>
                </div>
                <div class="card-body">
                    <div class="rounded">
                        <form class="comment-area-box">
                            <textarea rows="3" class="form-control comment border-0 resize-none" name="comment" placeholder="{{ trans('main.comment') }}..."></textarea>
                            <div class="p-2">
                                <button type="submit" data-area="0" class="btn newComm w-100 btn-success"><i class='fab fa-telegram-plane'></i> {{ trans('main.comment') }}</button>
                            </div>
                        </form>
                    </div> <!-- end .border-->
                </div>
            </div>
        </div> <!-- end col -->

        <div class="col-xl-4 col-lg-5">

            <div class="card">
                <div class="card-body">

                    <h5 class="card-title font-16 mb-3">{{ trans('main.info') }}</h5>
                    <div class="card mb-1 shadow-none border">
                        <div class="row w-100 ticketDetails first">
                            <div class="col-4">
                                <p>{{ trans('main.client') }}</p>
                            </div>
                            <div class="col-8">
                                <p>{{ $data->data->client }}</p>
                            </div>
                        </div>
                        <div class="row w-100 ticketDetails">
                            <div class="col-4">
                                <p>{{ trans('main.department') }}</p>
                            </div>
                            <div class="col-8">
                                <p>{{ $data->data->department }}</p>
                            </div>
                        </div>
                        <div class="row w-100 ticketDetails">
                            <div class="col-4">
                                <p>{{ trans('main.status') }}</p>
                            </div>
                            <div class="col-8">
                                <p>{{ $data->data->statusText }}</p>
                            </div>
                        </div>
                        <div class="row w-100 ticketDetails">
                            <div class="col-4">
                                <p>{{ trans('main.date') }}</p>
                            </div>
                            <div class="col-8">
                                <p>{{ date('d M y H:i A',strtotime($data->data->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="row w-100 ticketDetails last">
                            <div class="col-4">
                                <p>{{ trans('main.lastReply') }}</p>
                            </div>
                            <div class="col-8">
                                <p>{{ !empty($data->comments) ? $data->comments[0]->created_at : '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">

                    <h5 class="card-title font-16 mb-3">{{ trans('main.attachments') }}</h5>
                    @foreach($data->data->files as $oneFile)
                    <div class="m-2 border text-center">
                        <a href="{{ $oneFile->photo }}" target="_blank">
                            @if($oneFile->file_type == 'photo')
                            <img class="wd-150 mb-0" src="{{ $oneFile->photo }}" alt="attachment">
                            @elseif($oneFile->file_type == 'video')
                            <video src="{{ $oneFile->photo }}" controls>
                                <source src="{{ $oneFile->photo }}" type="video/mp4">
                            </video>
                            @endif
                        </a>
                        <h6 class="mb-0 p-3 bg-gray-100"> 
                            {{ $oneFile->photo_name }} <br>
                            <small class="text-muted">{{ $oneFile->photo_size }}</small>
                        </h6>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
</div> <!-- container -->
@endsection

@section('modals')
@include('tenant.Partials.photoswipe_modal')
@endsection


{{-- Scripts Section --}}
@section('scripts') 
<script src="{{ asset('components/comments.js') }}"></script>      
@endsection
