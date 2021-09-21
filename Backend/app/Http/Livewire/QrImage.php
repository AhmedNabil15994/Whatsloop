<?php

namespace App\Http\Livewire;

use Livewire\Component;

class QrImage extends Component
{

    protected $url = '';
    protected $area = '';
    public function getData(){
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $result = $mainWhatsLoopObj->status();
        $result = $result->json();
        
        $data['qrImage'] = asset('tenancy/assets/images/qr-load.png');
        $data['area'] = 1;
        
        if(isset($result['data'])){
            if($result['data']['accountStatus'] == 'got qr code'){
                if(isset($result['data']['qrCode'])){
                    $image = '/uploads/instanceImage' . time() . '.png';
                    $destinationPath = public_path() . $image;
                    $qrCode =  base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $result['data']['qrCode']));
                    // $succ = file_put_contents($destinationPath, $qrCode);   
                    $data['qrImage'] = mb_convert_encoding($result['data']['qrCode'], 'UTF-8', 'UTF-8');
                    $data['area'] = 0;
                }
            }else if($result['data']['accountStatus'] == 'authenticated'){
                $data['qrImage'] = asset('tenancy/assets/images/qr-load.png');
                $data['area'] = 1;
            }
        }

        $newData = [
            'url' => $data['qrImage'],
            'area' => $data['area'],
            'now' => 0,
        ];
        return $newData;
    }

    public function render(){
        $data = $this->getData();
        $this->url = $data['url'];
        $this->area = $data['area'];
        return view('livewire.qr-image', $data);
    }

    public function fetchNewImage(){
        $data = $this->getData();
        $this->url = $data['url'];
        $this->area = $data['area'];
    }
}
