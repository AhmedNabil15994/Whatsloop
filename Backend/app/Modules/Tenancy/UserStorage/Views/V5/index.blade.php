{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('content')
<div class="storage">
    <div class="row">
        <div class="col-md-4">
            <div class="size">
                <ul class="list">
                    <li class="{{ Active(URL::to('/storage')) }} {{ Active(URL::to('/storage/users*')) }}"><a href="{{ URL::to('/storage') }}">{{ trans('main.users') }} <i class="icon flaticon-group"></i></a></li>
                    @if(\Helper::checkRules('list-bots'))
                    <li><a href="{{ URL::to('/storage/bots') }}">{{ trans('main.bot') }} <i class="icon flaticon-robot"></i></a></li>
                    @endif
                    @if(\Helper::checkRules('list-group-messages'))
                    <li><a href="{{ URL::to('/storage/groupMsgs') }}">{{ trans('main.groupMsgs') }} <i class="icon flaticon-edit"></i></a></li>
                    @endif
                    @if(\Helper::checkRules('list-livechat'))
                    <li><a href="{{ URL::to('/storage/chats') }}">{{ trans('main.livechat') }} <i class="icon flaticon-statistics"></i></a></li>
                    @endif
                </ul>
                <div class="details">
                    <h2 class="titleSize">{{ trans('main.storages') }}</h2>
                    @php 
                        $result = round( ((int) $data->totalStorage > 0 ? (int) $data->totalSize / (int) $data->totalStorage : 0) ,2);
                    @endphp
                    <div class="progressSize">
                        <span class="line" style="width:{{ $result }}%"></span>
                    </div>
                    <span class="total">{{ $data->totalSize }} ({{ $result }}%) {{ trans('main.of') }} {{ $data->totalStorage / 1024 }} {{ trans('main.gigaB') }} {{ trans('main.used') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="files stats">
                <h2 class="titleFiles">{{ trans('main.files') }}</h2>
                <div class="msgArchive">
                    <div class="overflowTable">
                        <table class="products-table">
                            <thead>
                                <tr>
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
                                </tr>
                            </thead>
                            <tbody>
                                @if($data->parent == 'main')
                                    @foreach($data->data as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ URL::current().'/'.($data->type == 'users' ? $data->type.'/' : '').$item->id }}" class="text-reset">{{ $item->id }}</a>
                                            <i class="iconFile flaticon-folder"></i>
                                        </td>
                                        <td>{{ date('D m, Y',strtotime($item->created_at)) }}</td>
                                        <td>{{ $item->folder_size }}</td>
                                        <td>
                                            <div class="options">
                                                <i class="openOptions flaticon-menu-1"></i>
                                                <ul class="optionsList">
                                                    <li><a href="{{ URL::current().(Request::segment(2) != 'users' && Request::segment(2) == null ?  '/'.$data->type : '').'/'.$item->id }}">{{ trans('main.view') }}</a></li>
                                                    @if(\Helper::checkRules('delete-storage'))
                                                    <li><a href="{{ URL::current().(Request::segment(2) != 'users' && Request::segment(2) == null ?  '/'.$data->type : '').'/'.$item->id.'/remove' }}">{{ trans('main.delete') }}</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    @if($data->type == 'chats')
                                    @foreach($data->data as $key => $oneItem)
                                    @php
                                        $fileType = \ImagesHelper::checkExtensionType(array_reverse(explode('.' , $oneItem->file_name))[0],'getData')[0];
                                    @endphp
                                    <tr class="tr{{ $key }} {{ $oneItem->file_name != null ? 'hasImage' : '' }}">
                                        <td>
                                            <a href="{{ $oneItem->file }}" target="_blank" class="text-reset">
                                                @if($fileType == 'photo')
                                                <img class="float-left thumb" src="{{ $oneItem->file }}">
                                                @endif
                                            </a>
                                            <span class="name">
                                                @if($fileType != 'photo')
                                                <i class="iconFile flaticon-folder"></i> 
                                                @endif
                                                {{ $oneItem->file_name }}
                                            </span>
                                            
                                        </td>
                                        <td>-----</td>
                                        <td>{{ $oneItem->file_size }}</td>
                                        <td>
                                            <div class="options">
                                                <i class="openOptions flaticon-menu-1"></i>
                                                <ul class="optionsList">
                                                    <li><a href="{{ $oneItem->file }}">{{ trans('main.download') }}</a></li>
                                                    @if(\Helper::checkRules('delete-storage'))
                                                    <li><a onclick="deleteStorageFile('{{ URL::current().'/'.$oneItem->file_name.'/removeFile' }}')">{{ trans('main.delete') }}</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    @php
                                        $fileName = isset($data->data->photo) && $data->data->photo_name != null ? $data->data->photo_name : $data->data->file_name;
                                        $fileType = \ImagesHelper::checkExtensionType(array_reverse(explode('.' , $fileName))[0],'getData')[0];
                                    @endphp
                                    <tr class="tr{{ isset($data->data->photo) && $data->data->photo_name != null ? $data->data->photo_name : $data->data->file_name }} {{ isset($data->data->photo) && $data->data->photo_name != null ? 'hasImage' : '' }}">
                                        <td>
                                            <a href="{{ isset($data->data->photo) && $data->data->photo != null ? $data->data->photo : $data->data->file }}" target="_blank" class="text-reset">
                                                @if($fileType == 'photo')
                                                <img class="float-left thumb" src="{{ isset($data->data->photo) && $data->data->photo != null ? $data->data->photo : $data->data->file }}">
                                                @endif
                                            </a>
                                            <span class="name">
                                                @if($fileType != 'photo')
                                                <i class="iconFile flaticon-folder"></i>
                                                @endif
                                                {{ isset($data->data->photo_name) && $data->data->photo_name != null ? $data->data->photo_name : $data->data->file_name }}
                                            </span>
                                        </td>
                                        <td>{{ date('D m, Y',strtotime($data->data->created_at)) }}</td>
                                        <td>{{ isset($data->data->photo) && $data->data->photo_size != null ? $data->data->photo_size : $data->data->file_size }}</td>
                                        <td>
                                            <div class="options">
                                                <i class="openOptions flaticon-menu-1"></i>
                                                <ul class="optionsList">
                                                    <li><a href="{{ isset($data->data->photo) && $data->data->photo != null ? $data->data->photo : $data->data->file }}">{{ trans('main.download') }}</a></li>
                                                    @if(\Helper::checkRules('delete-storage'))
                                                    <li><a onclick="deleteStorageFile('{{ URL::current().'/remove' }}')">{{ trans('main.delete') }}</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection