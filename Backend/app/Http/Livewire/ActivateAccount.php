<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\UserChannels;
use App\Models\BankTransfer;


class ActivateAccount extends Component
{

    public $activateAccount = 0;
    public $transfer_order_no = '';
    public function mount($transfer_order_no=null){ 
        $this->transfer_order_no = $transfer_order_no;
    }


    public function activateAccount(){
        $this->activateAccount = 1;
    }

    public function render(){    
        $userObj  = User::first();
        $channelObj  = UserChannels::first();
        $data['activateAccount'] = 0;
        if(($userObj && $userObj->membership_id != null) && ($channelObj && $channelObj->end_date >= date('Y-m-d')) ){
            $data['activateAccount'] = 1;
            $this->emit('activateAccount');   
        }else{
            if($this->transfer_order_no != null){
                $transferObj = BankTransfer::where('user_id',$userObj->id)->where('order_no',$this->transfer_order_no)->orderBy('id','DESC')->first();
                if($transferObj && $transferObj->status == 2){
                    $data['activateAccount'] = 1;
                    $this->emit('activateAccount');   
                }
            }
        }
        return view('livewire.activate-account')->with('data',(object) $data);
    }
}
