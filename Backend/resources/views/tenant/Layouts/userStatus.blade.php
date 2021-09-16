@php
$userStatusObj = App\Models\UserStatus::orderBy('id','DESC')->first();
$data = [];
if(($userStatusObj && $userStatusObj->status != 1) || !$userStatusObj ){
    $mainWhatsLoopObj = new \MainWhatsLoop();
    $result = $mainWhatsLoopObj->status();
    $result = $result->json();
    if(isset($result['data'])){
        if($result['data']['accountStatus'] == 'got qr code'){
            if(isset($result['data']['qrCode'])){
                $data['qrImage'] = 1;
            }
        }
    }
}

$userAddonsTutorial = [];
$userAddons = array_unique(Session::get('addons'));
$addonsTutorial = [1,2,4,5];
for ($i = 0; $i < count($addonsTutorial) ; $i++) {
    if(in_array($addonsTutorial[$i],$userAddons)){
        $checkData = App\Models\Variable::getVar('MODULE_'.$addonsTutorial[$i]);
        if($checkData == ''){
            $varObj = new App\Models\Variable;
            $varObj->var_key = 'MODULE_'.$addonsTutorial[$i];
            $varObj->var_value = 0;
            $varObj->save();
            $userAddonsTutorial[] = $addonsTutorial[$i];
        }elseif($checkData == 0){
            $userAddonsTutorial[] = $addonsTutorial[$i];
        }
    }
}
$data['tutorials'] = array_values($userAddonsTutorial);
@endphp

@if(Request::segment(1) != 'QR' && Request::segment(1) != 'menu')
    <div class="container-fluid">
@endif
@if(isset($data['qrImage']) || (isset($data['tutorials']) && !empty($data['tutorials'])))
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="header-title">
                    <div class="row d-block w-100">
                        <div class="cols first">
                            <i class="si si-close text-danger"></i>
                        </div>
                        <div class="cols second">
                            @if(!isset($data['qrImage']))
                                <span class="text-danger">{{ trans('main.addonsConfigure') }}</span>
                                <a href="{{ URL::to('/QR') }}" class="btn btn-success float-right">{{ trans('main.reconfigure') }}</a>
                            @else
                                <span class="text-danger">{{ trans('main.gotQrCode') }}</span>
                                <a href="{{ URL::to('/QR') }}" class="btn btn-success float-right">{{ trans('main.reconnect') }}</a>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </h5>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
@endif

@if(Request::segment(1) != 'QR' && Request::segment(1) != 'menu')
    </div>
@endif
