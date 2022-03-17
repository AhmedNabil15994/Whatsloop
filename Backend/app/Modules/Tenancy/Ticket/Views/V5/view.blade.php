{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<style type="text/css" media="screen">
    .supportForm{
        padding: 20px;
    }
    .attachments img{
        width: 150px;
    }
    .mb-0.bg-gray-100{
        padding: 25px;
        font-size: 15px;
    }
    .files{
        width: 100%;
    }
    .files img,
    .files video{
        width: 100%;
        height: 400px;
    }
    .files a{
        padding: 15px;
    }
    .attach{
        width: auto;
        margin-left: 15px;
        margin-right: 15px;
    }
    .break{
        white-space: break-spaces;
    }
</style>
@endsection

@section('content')
<div class="tickets">
    <div class="row">
        <div class="col-md-8">
            <div class="ticketContent">
                <h2 class="title">{{ ucfirst($data->data->subject) }} <span>{{ trans('main.ticket') }} #{{ $data->data->id }}</span></h2>
                <div class="desc">{!! $data->data->description !!}</div>
            </div>
            <div class="ticketContent">
                <h2 class="title">{{ trans('main.comments') }} <span>({{ $data->commentsCount }})</span></h2>
                <div class="desc">
                    @foreach($data->comments as $comment)
                    <div class="comment {{ $data->data->user_id != $comment->created_by ? 'employee' : 'owner' }}">
                        <div class="commentHead">
                            <div class="creator">
                                <i class="icon flaticon-user-3"></i>
                                <span class="name">{{ $comment->creator }}</span>
                                <span class="definition">{{ $data->data->user_id != $comment->created_by ? trans('main.employee') : trans('main.owner') }}</span>
                            </div>
                            <span class="time">{{ $comment->created_at }}</span>
                        </div>
                        <div class="commentContent">
                            <div class="break">{!! $comment->comment !!}</div>
                            <div class="files">
                                @if($comment->file_name != null)
                                @if($comment->file_type == 'photo')
                                <img src="{{ $comment->file }}" alt="">
                                @elseif($comment->file_type == 'video')
                                <video width="320" height="240" controls>
                                    <source src="{{ $comment->file }}" type="video/mp4">
                                </video>
                                @else
                                <div class="nextPrev">
                                    <a href="{{ $comment->file }}" class="btnNext" target="_blank">{{ trans('main.download') }}</a>
                                </div>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="ticketContent">
                <h2 class="title">{{ trans('main.addComment') }} <i class="iconPlus fa fa-plus"></i></h2>
                <form class="desc addComment comment-area-box" enctype="multipart/form-data">
                    <textarea class="comment" name="comment" placeholder="{{ trans('main.comment') }}..."></textarea>
                    <div class="dropzone hidden" id="commentFile">
                        <div class="fallback">
                            <input name="file" type="commentAttachment" />
                        </div>
                        <div class="dz-message needsclick">
                            <i class="h1 si si-cloud-upload"></i>
                            <h3 class="text-center">{{ trans('main.dropzoneP') }}</h3>
                        </div>
                    </div>
                    <div class="clearfix">
                        <button type="submit" data-area="0" class="newComm btnStyle"> {{ trans('main.comment') }}</button>
                        <button type="submit" data-area="0" class="attach btnStyle mr-2"> {{ trans('main.attachments') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="details">
                <h2 class="title">{{ trans('main.info') }}</h2>
                <ul class="content">
                    <li>{{ trans('main.client') }} <span>{{ $data->data->client }}</span></li>
                    <li>{{ trans('main.department') }} <span>{{ $data->data->department }}</span></li>
                    <li>{{ trans('main.status') }} <span>{{ $data->data->statusText }}</span></li>
                    <li>{{ trans('main.date') }} <span>{{ date('d M y H:i A',strtotime($data->data->created_at)) }}</span></li>
                    <li>{{ trans('main.lastReply') }} <span>{{ !empty($data->comments) ? $data->comments[0]->created_at : '' }}</span></li>
                </ul>
            </div>
            <div class="attachments">
                <h2 class="title">{{ trans('main.attachments') }}</h2>
                <div class="uploads">
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
</div>
@endsection

@section('modals')
@include('tenant.Partials.photoswipe_modal')
@endsection


{{-- Scripts Section --}}
@section('scripts') 
<script src="{{ asset('V5/components/comments.js') }}"></script>      
@endsection
