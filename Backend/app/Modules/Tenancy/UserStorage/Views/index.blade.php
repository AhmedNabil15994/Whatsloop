{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<style type="text/css" media="screen">
	.mdi.mdi-dots-horizontal{
		color: #000;
	}
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <!-- Right Sidebar -->
        <div class="col-12">
            <div class="card-box row">
                <!-- Left sidebar -->
                <div class="inbox-leftbar col-3">
                    <div class="card">
                    	<div class="mail-list mt-3">
	                        <a href="{{ URL::to('/storage') }}" class="list-group-item border-0 {{ Active(URL::to('/storage')) }} {{ Active(URL::to('/storage/users*')) }}"><i class="mdi mdi-folder-outline font-18 align-middle mr-2"></i>{{ trans('main.users') }}</a>
	                        <a href="{{ URL::to('/storage/bots') }}" class="list-group-item border-0 {{ Active(URL::to('/storage/bots*')) }}"><i class="mdi mdi-folder-outline font-18 align-middle mr-2"></i>{{ trans('main.bot') }}</a>
	                        <a href="{{ URL::to('/storage/groupMsgs') }}" class="list-group-item border-0 {{ Active(URL::to('/storage/groupMsgs*')) }}"><i class="mdi mdi-folder-outline font-18 align-middle mr-2"></i>{{ trans('main.groupMsgs') }}</a>
	                        <a href="{{ URL::to('/storage/chats') }}" class="list-group-item border-0 {{ Active(URL::to('/storage/chats*')) }}"><i class="mdi mdi-folder-outline font-18 align-middle mr-2"></i>{{ trans('main.livechat') }}</a>
	                    </div>
	                    <div class="mt-5">
	                        <h6 class="text-uppercase mt-3">{{ trans('main.storages') }}</h6>
	                        <div class="progress my-2 progress-sm">
	                        	@php 
	                        		$result = (int) $data->totalStorage > 0 ? (int) $data->totalSize / (int) $data->totalStorage : 0;
	                        	@endphp
	                            <div class="progress-bar progress-lg bg-success" role="progressbar" style="width: {{ $result }}%" aria-valuenow="{{ $result }}" aria-valuemin="0" aria-valuemax="100"></div>
	                        </div>
	                        <p class="text-muted font-12 mb-0">{{ $data->totalSize }} ({{ $result }}%) of {{ $data->totalStorage / 1024 }} {{ trans('main.gigaB') }} used</p>
	                    </div>
                    </div>
                </div>
                <!-- End Left sidebar -->

                <div class="inbox-rightbar col-9">
                	<div class="card">
                		<div class="mt-3">
	                        <h5 class="mb-3">{{ trans('main.files') }}</h5>

	                        <div class="table-responsive">
	                            <table class="table table-centered table-nowrap mb-0">
	                                <thead class="thead-light">
	                                	@if($data->parent == 'main')
	                                    <tr>
	                                        <th class="border-0">{{ trans('main.name') }}</th>
	                                        <th class="border-0">{{ trans('main.date') }}</th>
	                                        <th class="border-0">{{ trans('main.size') }}</th>
	                                        <th class="border-0" style="width: 80px;">{{ trans('main.actions') }}</th>
	                                    </tr>
	                                    @else
	                                    <tr>
	                                        <th class="border-0">{{ trans('main.name') }}</th>
	                                        <th class="border-0">{{ trans('main.date') }}</th>
	                                        <th class="border-0">{{ trans('main.size') }}</th>
	                                        <th class="border-0" style="width: 80px;">{{ trans('main.actions') }}</th>
	                                    </tr>
	                                    @endif
	                                </thead>
	                                <tbody>
		                                @if($data->parent == 'main')
			                        		@foreach($data->data as $item)
			                                <tr>
			                                    <td class="border-0">
			                                        <i class="mdi mdi-folder-outline"></i>
			                                        <span class="ml-2 font-weight-medium">
			                                        	<a href="{{ URL::current().'/'.($data->type == 'users' ? $data->type.'/' : '').$item->id }}" class="text-reset">{{ $item->id }}</a>
			                                        </span>
			                                    </td>
			                                    <td class="border-0">
			                                        <p class="mb-0">{{ date('D m, Y',strtotime($item->created_at)) }}</p>
			                                    </td>
			                                    <td class="border-0">{{ $item->folder_size }}</td>
			                                    <td class="border-0">
			                                    	<div class="btn-group dropdown">
			                                            <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-xs" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-horizontal"></i></a>
			                                            <div class="dropdown-menu dropdown-menu-right">
			                                                <a class="dropdown-item" href="{{ URL::current().'/'.$data->type.'/'.$item->id }}"><i class="mdi mdi-eye mr-2 text-muted vertical-middle"></i>{{ trans('main.view') }}</a>
			                                                @if(\Helper::checkRules('delete-storage'))
			                                                <a class="dropdown-item" href="{{ URL::current().'/'.$data->type.'/'.$item->id.'/remove' }}"><i class="mdi mdi-delete mr-2 text-muted vertical-middle"></i>{{ trans('main.delete') }}</a>
			                                                @endif
			                                            </div>
			                                        </div>
			                                    </td>
			                                </tr>
		                           		 	@endforeach
			                                @else
			                                
			                                @if($data->type == 'chats')
			                                @foreach($data->data as $oneItem)
			                                <tr>
			                                    <td class="border-0">
			                                        <i class="mdi mdi-file-outline"></i>
			                                        <span class="ml-2 font-weight-medium">
			                                        	<a href="{{ $oneItem->file }}" target="_blank" class="text-reset">{{ $oneItem->file_name }}</a>
			                                        </span>
			                                    </td>
			                                    <td class="border-0">
			                                        <p class="mb-0">-----</p>
			                                    </td>
			                                    <td class="border-0">{{ $oneItem->file_size }}</td>
			                                    <td class="border-0">
			                                        <div class="btn-group dropdown">
			                                            <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-xs" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-horizontal"></i></a>
			                                            <div class="dropdown-menu dropdown-menu-right">
			                                                <a class="dropdown-item" href="{{ $oneItem->file }}" target="_blank"><i class="mdi mdi-download mr-2 text-muted vertical-middle"></i>{{ trans('main.download') }}</a>
			                                                @if(\Helper::checkRules('delete-storage'))
			                                                <a class="dropdown-item" href="{{ URL::current().'/'.$oneItem->file_name.'/removeFile' }}"><i class="mdi mdi-delete mr-2 text-muted vertical-middle"></i>{{ trans('main.delete') }}</a>
			                                                @endif
			                                            </div>
			                                        </div>
			                                    </td>
			                                </tr>
			                                @endforeach
			                                @else
			                                <tr>
			                                    <td class="border-0">
			                                        <i class="mdi mdi-file-outline"></i>
			                                        <span class="ml-2 font-weight-medium">
			                                        	<a href="{{ $data->data->photo != null ? $data->data->photo : $data->data->file }}" target="_blank" class="text-reset">{{ $data->data->photo_name != null ? $data->data->photo_name : $data->data->file_name }}</a>
			                                        </span>
			                                    </td>
			                                    <td class="border-0">
			                                        <p class="mb-0">{{ date('D m, Y',strtotime($data->data->created_at)) }}</p>
			                                    </td>
			                                    <td class="border-0">{{ $data->data->photo_size != null ? $data->data->photo_size : $data->data->file_size }}</td>
			                                    <td class="border-0">
			                                        <div class="btn-group dropdown">
			                                            <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-xs" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-horizontal"></i></a>
			                                            <div class="dropdown-menu dropdown-menu-right">
			                                                <a class="dropdown-item" href="{{ $data->data->photo != null ? $data->data->photo : $data->data->file }}" target="_blank"><i class="mdi mdi-download mr-2 text-muted vertical-middle"></i>{{ trans('main.download') }}</a>
			                                                @if(\Helper::checkRules('delete-storage'))
			                                                <a class="dropdown-item" href="{{ URL::current().'/remove' }}"><i class="mdi mdi-delete mr-2 text-muted vertical-middle"></i>{{ trans('main.delete') }}</a>
			                                                @endif
			                                            </div>
			                                        </div>
			                                    </td>
			                                </tr>
			                                @endif
			                            @endif
	                                </tbody>
	                            </table>
	                        </div>

	                    </div> <!-- end .mt-3-->
                	</div>
                </div> 
                <!-- end inbox-rightbar-->

                <div class="clearfix"></div>
            </div> <!-- end card-box -->

        </div> <!-- end Col -->
    </div>
<!-- end row-->
</div> <!-- container -->
@endsection

@section('scripts')
@endsection
