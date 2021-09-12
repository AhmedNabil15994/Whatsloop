{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['title'])

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!--begin::Card-->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="fas fa-sort-amount-up-alt"></i> {{ trans('main.sort').' '.$data->designElems['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <div class="tab-content">
                        <div class="tab-pane active" id="AddTabs" role="tabpanel">
                            <div class="forms">  
                                <!--begin::Card-->
                                <div class="card card-custom gutter-b">
                                    <div class="card-body">
                                        <ul class="sortable-list tasklist list-unstyled" id="upcoming">
                                            @foreach($data->data as $item)
                                            <li id="task1" class="border-0 bg-primary"  data-id="{{ $item->id }}">
                                                <h5 class="mt-0"><a href="javascript: void(0);">{{ $item->sort }} - {{ $item->{ $data->designElems['sortName'] } }}</a></h5>
                                                <div class="clearfix"></div>
                                                <div class="row"></div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <!--end::Card-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card-->
        </div>
    </div>   
</div>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('tenancy/assets/libs/sortablejs/Sortable.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/components/sorting.js') }}"></script>
@endsection
