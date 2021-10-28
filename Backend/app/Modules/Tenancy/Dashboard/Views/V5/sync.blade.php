@extends('tenant.Layouts.V5.master2')
@section('title',trans('main.syncAccount'))
@section('styles')

@endsection


{{-- Content --}}
@section('content')
<div class="Additions">
    <h2 class="title">{{ trans('main.sync_p') }} (<span class="num">{{ count($data) }}</span>)</h2>
    <a href="#" class="btnAdd sync"></a>
</div> 

<form class="form-horizontal" method="POST" action="{{ URL::current() }}">
	@csrf
	<div class="categories clearfix">
	    <div class="row">

	        @if(in_array('chat',$data))
	        <div class="linkStyle card selected" data-area="chat">
	            <a href="#" class="link active">
	                <i class="icon flaticon-statistics"></i> {{ trans('main.livechat') }}
	            </a>
	        </div>
	        @endif

	        @if(in_array('salla',$data))
	        <div class="linkStyle card selected" data-area="salla">
	        	<a href="#" class="link active">
	                <i class="icon flaticon-shopping-bag"></i> {{ trans('main.salla') }}
	            </a>
	        </div>
	        @endif

			@if(in_array('zid',$data))
	        <div class="linkStyle card selected" data-area="zid">
	            <a href="#" class="link active">
	                <i class="icon">
	                    <svg xmlns="http://www.w3.org/2000/svg" width="31.158" height="32.769" viewBox="0 0 31.158 32.769">
	                      <path id="Path_1275" data-name="Path 1275" d="M119.9,218.925a3.281,3.281,0,0,1,3.8-3.273,19.364,19.364,0,0,1,8.138,3.73,14.581,14.581,0,0,1,4.776,7.6c.52,2.054.873,5.131-1.089,6.572-1.722,1.265-3.318-.131-3.736-1.865-.782-3.242.464-6.3,2.427-8.846a19.086,19.086,0,0,1,7.06-5.744,16.448,16.448,0,0,1,4.79-1.151,2.743,2.743,0,0,1,3,2.738h0a2.743,2.743,0,0,1-3,2.739,16.447,16.447,0,0,1-4.79-1.152,19.075,19.075,0,0,1-7.06-5.744c-1.964-2.548-3.209-5.6-2.427-8.845.418-1.735,2.014-3.13,3.736-1.865,1.962,1.441,1.609,4.518,1.089,6.572a14.581,14.581,0,0,1-4.776,7.6,19.954,19.954,0,0,1-8.341,3.925,3.047,3.047,0,0,1-3.592-2.992Z" transform="translate(-118.903 -202.306)" fill="none" stroke="#9499a4" stroke-miterlimit="10" stroke-width="2"/>
	                    </svg>
	                </i> {{ trans('main.zid') }}
	            </a>
	        </div>
	        @endif

	        @if(in_array('users',$data))
	        <div class="linkStyle card selected" data-area="users">
	            <a href="#" class="link active">
	                <i class="icon flaticon-group"></i> {{ trans('main.users') }}
	            </a>
	        </div>
	        @endif

	        @if(in_array('groups',$data))
	        <div class="linkStyle card selected" data-area="groups">
	            <a href="#" class="link active">
	                <i class="icon flaticon-group"></i> {{ trans('main.groups') }}
	            </a>
	        </div>
	    	@endif

			@if(in_array('bot',$data))	        
	        <div class="linkStyle card selected" data-area="bot">
	            <a href="#" class="link active">
	                <i class="icon flaticon-robot"></i> {{ trans('main.bot') }}
	            </a>
	        </div>
	        @endif
	        @if(in_array('quick_reply',$data))
	        <div class="linkStyle card selected" data-area="quick_reply">
	            <a href="#" class="link active">
	                <i class="icon flaticon-chat-bubble"></i> {{ trans('main.replies') }}
	            </a>
	        </div>
	        @endif
			@if(in_array('tags',$data))
	        <div class="linkStyle card selected" data-area="tags">
	            <a href="#" class="link active">
	                <i class="icon flaticon-menu"></i> {{ trans('main.categories') }}
	            </a>
	        </div>
	        @endif
	        @if(in_array('contacts',$data))
	        <div class="linkStyle card selected" data-area="contacts">
	            <a href="#" class="link active">
	                <i class="icon flaticon-add-group"></i> {{ trans('main.contacts') }}
	            </a>
	        </div>
	        @endif
	        @if(in_array('groupNumbers',$data))
	        <div class="linkStyle card selected" data-area="groupNumbers">
	            <a href="#" class="link active">
	                <i class="icon flaticon-add-group"></i> {{ trans('main.groupNumbers') }}
	            </a>
	        </div>
	        @endif
			@if(in_array('group_messages',$data))
	        <div class="linkStyle card selected" data-area="chat">
	            <a href="#" class="link active">
	                <i class="icon flaticon-edit"></i> {{ trans('main.groupMsgs') }}
	            </a>
	        </div>
	        @endif
	    </div>
	</div>
	<!-- /row -->
	<div class="row">
	    <input type="hidden" name="data" value="{{ json_encode($data) }}">
	    <button name="Submit" type="submit" class="btnAdd AddBTN" id="SubmitBTN">{{ trans('main.syncAccount') }}</button>
	</div>
</form>

@endsection

{{-- Scripts Section --}}
@section('topScripts')
{{-- <script src="{{ asset('components/sync.js') }}"></script> --}}
@endsection
