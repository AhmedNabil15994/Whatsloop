<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Variable;

class QrImage extends Component
{

    protected $url = '';
    protected $area = '';

    public function render(){    
        $data['url'] = asset('images/qr-load.png');
        $data['area'] = 1;
        $varObj = Variable::getVar('QRIMAGE');
        if($varObj != null){
            $data['url'] = mb_convert_encoding($varObj, 'UTF-8', 'UTF-8');
            $data['area'] = 0;
        }else if($result['data']['accountStatus'] == 'authenticated'){
            $data['url'] = asset('images/qr-load.png');
            $data['area'] = 1;
            Variable::where('var_key','QRIMAGE')->delete();
        }
        return view('livewire.qr-image')->with('data',(object) $data);
    }
}
