<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\UserStatus;
use App\Models\UserAddon;
use App\Models\User;
use App\Models\Variable;
use App\Models\Tenant;
use Session;

class CheckReconnection extends Component
{
    protected $haveImage = '';
    protected $tutorials = '';
    protected $seconds = 2;
    public $requestSemgent;
    public $addons;
    public $tenant_id;

    public function mount($requestSemgent,$addons,$tenant_id){ 
        $this->requestSemgent = $requestSemgent;
        $this->addons = $addons;
        $this->tenant_id = $tenant_id;
    }

    public function render(){   
        \Artisan::call('tenants:run instance:status --tenants='.$this->tenant_id);
        $userStatusObj = UserStatus::orderBy('id','DESC')->first();

        $data = [];
        $data['haveImage'] = 0;
        $data['dis'] = 0;
        $data['seconds'] = 2;
        $varObj = Variable::getVar('QRIMAGE') ;

        if(isset($userStatusObj) && $userStatusObj->status == 4 && $varObj != null){            
            $data['haveImage'] = 1;
            $data['dis'] = 1;
        }elseif(isset($userStatusObj) && in_array($userStatusObj->status,[2,3])){
            $data['seconds'] = 60;
        }
            
        $userAddonsTutorial = [];
        $userAddons = array_unique($this->addons);
        $addonsTutorial = [1,2,4,5];
        $userObj = User::first();
        for ($i = 0; $i < count($addonsTutorial) ; $i++) {
            if(in_array($addonsTutorial[$i],$userAddons) && UserAddon::where('status',1)->where('addon_id',$addonsTutorial[$i])->where('user_id',$userObj->id)->first()){
                $checkData = Variable::getVar('MODULE_'.$addonsTutorial[$i]);
                if($checkData == ''){
                    $varObj = new Variable;
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
        return view('livewire.check-reconnection')->with('data',(object) $data);
    }
}
