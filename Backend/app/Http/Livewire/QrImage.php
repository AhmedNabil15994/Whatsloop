<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Variable;

class QrImage extends Component
{

    protected $url = '';
    protected $area = '';

    public $showLoadingQR = 0;

    public function statusChanged()
    {
        $this->showLoadingQR = 1;
    }

    public function render(){    
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $result = $mainWhatsLoopObj->status();
        $result = $result->json();
        
        $data['url'] = asset('images/qr-load.png');
        $data['area'] = 1;
        
        if(isset($result['data'])){
            Variable::where('var_key','QRIMAGE')->delete();
            if($result['data']['accountStatus'] == 'got qr code'){
                if(isset($result['data']['qrCode'])){
                    $image = '/uploads/instanceImage' . time() . '.png';
                    $destinationPath = public_path() . $image;
                    $qrCode =  base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $result['data']['qrCode']));
                    $data['url'] = mb_convert_encoding($result['data']['qrCode'], 'UTF-8', 'UTF-8');
                    $data['area'] = 0;
                    Variable::insert([
                        'var_key' => 'QRIMAGE',
                        'var_value' => $result['data']['qrCode'],
                    ]);
                }
            }else if($result['data']['accountStatus'] == 'authenticated'){
                $data['url'] = asset('images/qr-load.png');
                $data['area'] = 1;
                $this->emit('statusChanged'); 
            }
        }        
        return view('livewire.qr-image')->with('data',(object) $data);
    }
}
