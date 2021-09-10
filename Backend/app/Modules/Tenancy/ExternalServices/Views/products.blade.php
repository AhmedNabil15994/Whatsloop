{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
@endsection

@section('content')
<!-- row -->
<div class="row row-sm">
    <input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">
    <div class="col-xl-3 col-lg-4 mb-3 mb-md-0">
        <form action="{{ URL::current() }}" method="get" accept-charset="utf-8">
            <div class="card overflow-hidden">
                <h5 class="m-0 p-3 card-title bg-white border-bottom">{{ trans('main.search') }}</h5>
                <div class="py-3 px-3">
                    <div class="input-group">
                        <input type="text" name="name" value="{{ Request::get('name') }}" class="form-control" placeholder="{{ trans('main.search') }} ...">
                        <span class="input-group-append">
                            <button class="btn btn-primary" type="submit">{{ trans('main.search') }}</button>
                        </span>
                    </div>
                    @if(count(Request::all()) || $data->dis != 1)
                        @if(count(Request::all()))
                        <h5 class="m-0 p-3 card-title bg-white border-bottom">{{ trans('main.actions') }}</h5>
                        <a href="{{ URL::current() }}" class="btn btn-success btn-block mt-4" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.back') }}">
                            <i class=" fas fa-undo-alt"></i>
                            {{ trans('main.back') }}
                        </a>
                        @endif
                    @endif
                </div>
            </div>
            <div class="card overflow-hidden">
                <h5 class="m-0 p-3 card-title bg-white border-bottom border-top">{{ trans('main.price') }}</h5>
                <div class="p-3 d-flex align-items-center">
                    <div class="w-100">
                        <input placeholder="{{ trans('main.price') }}" value="{{ Request::get('price') }}" name="price" class="form-control rounded-0" />
                    </div>
                    {{-- <span class="h4 m-0 font-weight-normal px-2">-</span>
                    <div class="w-50">
                        <input placeholder="Up to" name="to" class="form-control rounded-0" />
                    </div> --}}
                </div>
            </div>
            <div class="card overflow-hidden">
                @if($data->dis != 1)
                <a href="{{ URL::current().'?refresh=refresh' }}" class="btn btn-success mt-4 mg-15 float-right" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.refresh') }}">
                    <i class=" fas fa-database"></i>
                    {{ trans('main.refresh') }}
                </a>
                @endif
            </div>
        </form>
    </div>
    <div class="col-xl-9 col-lg-8">
        <div class="row row-sm">
            @foreach($data->data as $product)
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4">
                <div class="product-card card overflow-hidden">
                    <img class="w-100 mt-0" src="{{ $product->images != null ? $product->images : asset('images/not-available.jpg') }}" alt="product-image"/>
                    <div class="card-body h-100">
                        <div class="d-flex">
                            @if($product->status == 1)
                            <span class="text-muted small mg-b-5">{{ trans('main.avail') }}</span>
                            <span class="ml-auto"><i class="fa fa-heart text-success"></i></span>
                            @else
                            <span class="text-muted small mg-b-5">{{ trans('main.unAvail') }}</span>
                            <span class="ml-auto"><i class="fa fa-heart text-danger"></i></span>
                            @endif
                        </div>
                        <h3 class="h6 mb-2 font-weight-bold text-uppercase">{{ $product->{'name_'.LANGUAGE_PREF} }}</h3>
                        <div class="d-flex">
                            <h4 class="h5 w-50 font-weight-bold text-danger">{{ $product->price }}</h4>
                            @foreach($product->{'categories_'.LANGUAGE_PREF} as $category)
                            <span class="tx-15 ml-auto">{{ $category }}</span>
                            @endforeach
                        </div>
                        <button class="btn btn-primary btn-block mb-0 mt-4">
                            <i class="fe fe-shopping-cart mr-1"></i>
                            {{ trans('main.quantity') }} : {{ $product->quantity }} {{ trans('main.piece') }}
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @include('tenant.Partials.pagination')
    </div>
</div>
<!-- row closed -->
@endsection

@section('modals')
@include('tenant.Partials.search_modal')
@endsection

{{-- Scripts Section --}}

@section('scripts')

@endsection
