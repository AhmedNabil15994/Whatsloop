<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\UserStatus;
use App\Models\Variable;
use App\Models\Tenant;
use Session;

class CheckReconnection extends Component
{
    protected $haveImage = '';
    protected $tutorials = '';
    public $requestSemgent;
    public $addons;

    public function mount($requestSemgent,$addons){ 
        $this->requestSemgent = $requestSemgent;
        $this->addons = $addons;
    }

    public function getData(){
        $userStatusObj = UserStatus::orderBy('id','DESC')->first();

        $data = [];
        $data['haveImage'] = 0;

        if(($userStatusObj && $userStatusObj->status != 1) || !$userStatusObj ){
            $mainWhatsLoopObj = new \MainWhatsLoop();
            $result = $mainWhatsLoopObj->status();
            $result = $result->json();
            if(isset($result['data'])){
                if($result['data']['accountStatus'] == 'got qr code'){
                    if(isset($result['data']['qrCode'])){
                        $data['haveImage'] = 1;
                    }
                }
            }
        }
            
        $userAddonsTutorial = [];
        $userAddons = array_unique($this->addons);
        $addonsTutorial = [1,2,4,5];
        for ($i = 0; $i < count($addonsTutorial) ; $i++) {
            if(in_array($addonsTutorial[$i],$userAddons)){
                tenancy()->initialize($tenant);
                $checkData = Variable::getVar('MODULE_'.$addonsTutorial[$i]);
                tenancy()->end($tenant);
                if($checkData == ''){
                    tenancy()->initialize($tenant);
                    $varObj = new Variable;
                    $varObj->var_key = 'MODULE_'.$addonsTutorial[$i];
                    $varObj->var_value = 0;
                    $varObj->save();
                    tenancy()->end($tenant);
                    $userAddonsTutorial[] = $addonsTutorial[$i];
                }elseif($checkData == 0){
                    $userAddonsTutorial[] = $addonsTutorial[$i];
                }
            }
        }

        $data['tutorials'] = array_values($userAddonsTutorial);
        return $data;
    }

    public function render(){   
        $data = $this->getData();
        $this->haveImage = $data['haveImage'];
        $this->tutorials = $data['tutorials'];
        return view('livewire.check-reconnection',$data);
    }

    public function checkStatus(){
        $data = $this->getData();
        $this->haveImage = $data['haveImage'];
        $this->tutorials = $data['tutorials'];
    }
}
