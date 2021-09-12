@extends('tenant.Layouts.master')
@section('title',trans('main.bundles'))
@section('styles')
<style type="text/css" media="screen">
    .features{
        /*min-height: 260px;*/
    }
    .features.unlim{
        min-height: 395px;
    }
    .features.unlim .card-body.text-center.border-top{
        padding-bottom: 3.5rem;
        border-bottom: 1px solid #edeef7 !important;
    }
</style>
@endsection


{{-- Content --}}
@section('content')
    <!-- row -->
    <div class="row text-center mg-t-20 mg-b-20 d-block">
        <h2 class="header-title h2 tx-bold">{{ trans('main.packages_h') }}</h2>
        <p class="tx-18 mg-b-20">{{ trans('main.packages_p') }} </p>
        <div class="form-group">
            <label class="custom-switch pl-0">
                <span class="mg-r-20 mg-l-20 tx-bold tx-22"> {{ trans('main.monthly') }} </span>
                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input durations">
                <span class="custom-switch-indicator"></span>
                <span class="mg-r-20 mg-l-20 tx-bold tx-22"> {{ trans('main.yearly') }} </span>
            </label> 
        </div>
    </div>
    <div class="row">
        @foreach($data->bundles as $key => $bundle)
        <div class="col-sm-6 col-lg-6 col-xl-3">
            <div class="card pricing-card overflow-hidden">
                {{-- <div class="card-status"></div> --}}
                <div class="card-body text-center mg-t-25">
                    <div class="card-title mb-0 tx-22">
                        {{ $bundle->title }}
                    </div>
                </div>
                <div class="card-body text-center border-0 pd-t-0 pd-b-0">
                    <div class="display-5 monthlyDisplay font-weight-semibold  my-2">
                        {{ $bundle->monthly_after_vat }} 
                        <span class="tx-16 priceSpan tx-bold">{{ trans('main.sar2') }}</span>
                    </div>
                    <div class="display-5 annualDisplay d-hidden font-weight-semibold  my-2">
                        {{ $bundle->annual_after_vat }} 
                        <span class="tx-16 priceSpan tx-bold">{{ trans('main.sar2') }}</span>
                    </div>
                    <p class="text-muted">{{ trans('main.packageP') }}</p>
                </div>
                <div class="features border-top">
                    <div class="card-body text-center pd-b-0">
                        <div class="text-center mt-2">
                            <div class="display-6">{!! $bundle->description !!}</div>
                        </div>
                    </div>  
                </div>
                <div class="card-body text-center">
                    <div class="text-center mt-6">
                        <a href="{{ $bundle->id == 6 ?  URL::to('/checkout?membership_id=1') :  URL::to('/postBundle/'.$bundle->id) }}" class="btn btn-primary btn-block">{{ trans('main.subscribe') }}</a>
                    </div>
                </div>
            </div>
        </div><!-- col-end -->
        @endforeach
    </div>
    <!-- end row -->
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('js/pages/dashboard-3.init.js') }}"></script>
@endsection

@section('scripts')
<script>
    $(function () {
        $('.durations').on('change',function(e){
            e.preventDefault();
            e.stopPropagation();
            if($(this).is(':checked')){
                $('.monthlyDisplay').addClass('d-hidden');
                $('.annualDisplay').removeClass('d-hidden');
            } else{ 
                $('.monthlyDisplay').removeClass('d-hidden');
                $('.annualDisplay').addClass('d-hidden');
            }
        });
    }); 
</script>
@endsection
