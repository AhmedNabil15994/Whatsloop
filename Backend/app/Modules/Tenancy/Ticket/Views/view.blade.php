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
            <div class="card d-block">
                <div class="card-header">
                    <h3 class="header-title"><i class="ti-receipt"></i> {{ trans('main.ticket') }} #{{ $data->data->id }}</h3>
                    <hr>
                </div>
                <div class="card-body">
                    <div class="float-right">
                        <div class="form-row">
                            <div class="col-auto">
                                <p class="btn btn-sm btn-link"> <i class="fa fa-calendar"></i> {{ date('d M y',strtotime($data->data->created_at)) }} <small class="text-muted">{{ date('H:i A',strtotime($data->data->created_at)) }}</small></p>
                            </div>
                            <div class="col-auto">
                                <select class="custom-select custom-select-sm form">
                                    <option selected="">{{ $data->data->statusText }}</option>
                                </select>
                            </div>
                        </div>
                    </div> <!-- end dropdown-->

                    <h4 class="mb-3 mt-0 font-18">{{ ucfirst($data->data->subject) }}</h4>

                    <div class="clerfix"></div>

                    <div class="row">
                        <div class="col-md-4">
                            <!-- Ticket type -->
                            <label class="mt-2 mb-1">{{ trans('main.department') }} :</label>
                            <p>
                                <i class='mdi mdi-ticket font-18 text-success mr-1 align-middle'></i> {{ $data->data->department }}
                            </p>
                            <!-- end Ticket Type -->
                        </div>
                    </div> <!-- end row -->

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Reported by -->
                            <label class="mt-2 mb-1">{{ trans('main.client') }} :</label>
                            <div class="media">
                                <img src="{{ $data->data->client_image }}" alt="Arya S"
                                    class="rounded-circle mr-2" height="24" />
                                <div class="media-body">
                                    <p> {{ $data->data->client }} </p>
                                </div>
                            </div>
                            <!-- end Reported by -->
                        </div> <!-- end col -->
                        @if(!empty($data->data->assignment))
                        <div class="col-md-6">
                            <!-- assignee -->
                            <label class="mt-2 mb-1">{{ trans('main.assignment') }} :</label>
                            @foreach($data->data->assignment as $user)
                            @php 
                            $assUser = \App\Models\CentralUser::getOne($user);
                            $assignedUser = \App\Models\CentralUser::getData($assUser);
                            @endphp
                            <div class="media mb-1">
                                <img src="{{ \App\Models\User::selectImage($assUser) }}" alt="Arya S"
                                    class="rounded-circle mr-2" height="24" />
                                <div class="media-body">
                                    <p> {{ $assignedUser->name }} </p>
                                </div>
                            </div>
                            @endforeach
                            <!-- end assignee -->
                        </div> <!-- end col -->
                        @endif
                    </div> <!-- end row -->

                    <label class="mt-4 mb-1">{{ trans('main.messageContent') }} :</label>

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
                    <div class="card-box" style="margin-bottom: 0;border-bottom: 1px solid #eee;" id="tableRaw{{ $comment->id }}">
                        <div class="media">
                            <img class="mr-2 avatar-sm rounded-circle" src="{{ $comment->image }}" alt="Generic placeholder image">
                            <div class="media-body">
                                <div class="dropdown float-right text-muted">
                                    <a href="#" class="dropdown-toggle text-muted font-18" data-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-dots-horizontal"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" style="">
                                        <!-- item-->
                                        {{-- @if(\Helper::checkRules('updateComment-'.$data->designElems['mainData']['nameOne']) || $comment->created_by == USER_ID)
                                        <a href="javascript:void(0);" class="dropdown-item">{{ trans('main.edit') }}</a>
                                        @endif --}}
                                        @if(\Helper::checkRules('deleteComment-'.$data->designElems['mainData']['nameOne']) || $comment->created_by == USER_ID)
                                        <!-- item-->
                                        <a onclick="deleteComment({{ $comment->id }})" class="dropdown-item">{{ trans('main.delete') }}</a>
                                        @endif
                                    </div>
                                </div>
                                <h5 class="m-0"><a href="contacts-profile.html" class="text-reset">{{ $comment->creator }}</a></h5>
                                <p class="text-muted"><small>{{ $comment->created_at }}</small></p>
                                <div class="font-16 font-italic text-dark">
                                    {!! $comment->comment !!}
                                </div>
                                @if(\Helper::checkRules('addComment-'.$data->designElems['mainData']['nameOne']))
                                <a data-area="{{ $comment->id }}" class="text-muted reply font-13 d-inline-block"><i class="mdi mdi-reply"></i> Reply</a>
                                @endif
                            </div>
                        </div>
                        @if(!empty($comment->replies))
                        <div class="post-user-comment-box mt-2 mr-2 ml-2">
                            @foreach($comment->replies as $reply)
                            <div class="media">
                                <img class="mr-2 avatar-sm rounded-circle" src="{{ $reply->image }}" alt="Generic placeholder image">
                                <div class="media-body">
                                    <h5 class="mt-0">
                                        <a href="contacts-profile.html" class="text-reset">{{ $reply->creator }}</a> 
                                        <br>
                                        <small class="text-muted">{{ $reply->created_at }}</small>
                                    </h5>
                                    {!! $reply->comment !!}
                                    <br>
                                    @if(\Helper::checkRules('addComment-'.$data->designElems['mainData']['nameOne']))
                                    <a data-area="{{ $reply->id }}" class="text-muted reply font-13 d-inline-block"><i class="mdi mdi-reply"></i> Reply</a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                
                    <div class="border rounded">
                        <form class="comment-area-box">
                            <textarea rows="3" class="form-control comment border-0 resize-none" name="comment" placeholder="{{ trans('main.comment') }}..."></textarea>
                            <div class="p-2 bg-light d-flex justify-content-between align-items-center">
                                <div></div>
                                <button type="submit" data-area="0" class="btn newComm btn-sm btn-success"><i class='fab fa-telegram-plane'></i> {{ trans('main.comment') }}</button>
                            </div>
                        </form>
                    </div> <!-- end .border-->

                </div> <!-- end card-body-->
            </div>
            <!-- end card-->
        </div> <!-- end col -->

        <div class="col-xl-4 col-lg-5">

            <div class="card">
                <div class="card-body">

                    <h5 class="card-title font-16 mb-3">{{ trans('main.attachments') }}</h5>
                    @foreach($data->data->files as $oneFile)
                    <div class="card mb-1 shadow-none border">
                        <div class="p-2">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar-sm">
                                        <span class="avatar-title badge-soft-primary text-primary rounded">
                                            {{ ucwords(explode('.', $oneFile->photo_name)[1]) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col pl-0">
                                    <a href="javascript:void(0);" class="text-muted font-weight-bold">{{ $oneFile->photo_name }}</a>
                                    <p class="mb-0 font-12">{{ $oneFile->photo_size }}</p>
                                </div>
                                <div class="col-auto">
                                    <!-- Button -->
                                    <a href="{{ $oneFile->photo }}" target="_blank" class="btn btn-link font-16 text-muted">
                                        <i class="dripicons-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
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
