<?php 

use Livewire\Component;
 
class QRImage extends Component
{
    public $url = '';
    public function render()
    {

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $result = $mainWhatsLoopObj->status();
        $result = $result->json();
        if(isset($result['data'])){
            if($result['data']['accountStatus'] == 'got qr code'){
                if(isset($result['data']['qrCode'])){
                    $image = '/uploads/instanceImage' . time() . '.png';
                    $destinationPath = public_path() . $image;
                    $qrCode =  base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $result['data']['qrCode']));
                    // $succ = file_put_contents($destinationPath, $qrCode);   
                    $data['qrImage'] = mb_convert_encoding($result['data']['qrCode'], 'UTF-8', 'UTF-8');
                }
            }
        }else if($result['data']['accountStatus'] == 'authenticated'){
            $data['qrImage'] = asset('images/qr-load.png');
        }

        return view('tenant.livewire.QR-Image', [
            'url' => $data['qrImage'],
        ]);
    }
}