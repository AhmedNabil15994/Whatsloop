@if($requestSemgent != 'QR' && $requestSemgent != 'menu')
    <div class="container-fluid"  wire:poll.2s wire:model="CheckReconnection">
        <div class="row">
    
@else
    <div class="row"  wire:poll.2s wire:model="CheckReconnection">
@endif
    @if((isset($data->haveImage) && $data->haveImage == 1) || (isset($data->tutorials) && !empty($data->tutorials)))
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title">
                        <div class="row d-block w-100">
                            <div class="cols first">
                                <i class="si si-close text-danger"></i>
                            </div>
                            <div class="cols second">
                                @if($data->haveImage != 1)
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

@if($requestSemgent != 'QR' && $requestSemgent != 'menu')
        </div>
    </div>
@else
    </div>
@endif
