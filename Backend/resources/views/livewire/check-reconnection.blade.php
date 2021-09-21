@if(Request::segment(1) != 'QR' && Request::segment(1) != 'menu')
    <div class="container-fluid"  wire:poll.2s="checkStatus" wire:model="CheckReconnection">
        <div class="row">
@else
    <div class="row"  wire:poll.2s="checkStatus" wire:model="CheckReconnection">
@endif
    @if(isset($haveImage) && $haveImage == 1)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title">
                        <div class="row d-block w-100">
                            <div class="cols first">
                                <i class="si si-close text-danger"></i>
                            </div>
                            <div class="cols second">
                                @if($haveImage != 1)
                                    <span class="text-danger">{{ trans('main.addonsConfigure') }}</span>
                                    <a href="/QR" class="btn btn-success float-right">{{ trans('main.reconfigure') }}</a>
                                @else
                                    <span class="text-danger">{{ trans('main.gotQrCode') }}</span>
                                    <a href="/QR" class="btn btn-success float-right">{{ trans('main.reconnect') }}</a>
                                @endif
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </h5>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    @endif

@if(Request::segment(1) != 'QR' && Request::segment(1) != 'menu')
        </div>
    </div>
@else
    </div>
@endif
