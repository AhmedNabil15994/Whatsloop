@extends('tenant.Layouts.master')
@section('title',trans('main.syncAccount'))
@section('styles')

@endsection


{{-- Content --}}
@section('content')
<!-- row -->
<div class="row">
	<div class="col-12">
		 <div class="card">
            <div class="card-body">
                <h5 class="header-title">
                    <div class="row d-block w-100">
                        <div class="cols first">
                            <i class="si si-info text-danger"></i>
                        </div>
                        <div class="cols second">
                            <span class="text-danger">{{ trans('main.sync_p') }} (<span class="num">{{ count($data) }}</span>)</span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </h5>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
	</div>
</div>

<form class="form-horizontal" method="POST" action="{{ URL::current() }}">
	@csrf
	<div class="row row-sm">
		@if(in_array('users',$data))
		<div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="users">
	            <a href="#" title="">
	                <div class="card-body">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/users.svg') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.users') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('groups',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="groups">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/users.svg') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.groups') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('groupNumbers',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="groupNumbers">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/contacts.svg') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.groupNumbers') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('contacts',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="contacts">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/contacts.svg') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.contacts') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('chat',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="chat">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/chat.svg') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.livechat') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('bot',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="bot">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/chatbot.png') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.chatBot') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('group_messages',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="group_messages">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/group_messages.svg') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.groupMsgs') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('quick_reply',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="quick_reply">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/quick_reply.svg') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.replies') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('tags',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="tags">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/tags.svg') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.categories') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('salla',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="salla">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/salla.svg') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.salla') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif

		@if(in_array('zid',$data))
	    <div class="col-xl-3 sync-item col-lg-6 col-sm-6 col-md-6">
	        <div class="card text-center selected" data-area="zid">
	            <a href="#" title="">
	                <div class="card-body ">
	                    <div class="feature widget-2 text-center mt-0 mb-3">
	                        <img src="{{ asset('images/zid.png') }}" alt="">
	                    </div>
	                    <h6 class="mb-1 text-muted">{{ trans('main.zid') }}</h6>
	                </div>
	            </a>
	        </div>
	    </div>
	    @endif
	</div>

	<!-- /row -->
	<div class="form-group justify-content-end row">
	    <div class="col-9 text-right">
	    	<input type="hidden" name="data" value="{{ json_encode($data) }}">
	        <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.syncAccount') }}</button>
	    </div>
	</div>
</form>
@endsection

{{-- Scripts Section --}}
@section('topScripts')
{{-- <script src="{{ asset('components/sync.js') }}"></script> --}}
@endsection
