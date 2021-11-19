@if((isset($data->haveImage) && $data->haveImage == 1) || (isset($data->tutorials) && !empty($data->tutorials)))
    <div class="Additions" wire:poll.{{ $data->seconds }}s wire:model="CheckReconnection">
        <h2 class="title">{{ $data->haveImage != 1 ? trans('main.addonsConfigure')  : trans('main.gotQrCode') }}</h2>
        <a href="/QR" class="btnAdd">{{ $data->haveImage != 1 ? trans('main.reconfigure') : trans('main.reconnect') }}</a>
    </div> 
@endif