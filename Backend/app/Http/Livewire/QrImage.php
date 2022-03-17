<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Jobs\SyncDialogsJob;
use App\Jobs\SyncMessagesJob;
use App\Models\ChatDialog;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Category;
use App\Models\CentralChannel;
use App\Models\UserChannels;
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
        $channelObj =  UserChannels::first();
        
        $data['url'] = asset('images/qr-load.png');
        $data['area'] = 1;
        
        if(isset($result['data'])){
            Variable::where('var_key','QRIMAGE')->delete();
            Variable::where('var_key','QRSYNCING')->delete();
            if($result['data']['accountStatus'] == 'got qr code'){
                if(isset($result['data']['qrCode'])){
                    $image = '/uploads/instance'.$channelObj->id.'Image' . time() . '.png';
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
                
                Variable::insert([
                    'var_key' => 'QRSYNCING',
                    'var_value' => 1,
                ]);

                // // Update User With Settings For Whatsapp Based On His Domain
                $domain = User::first()->domain;
                $myData = [
                    'sendDelay' => '0',
                    'webhookUrl' => str_replace('://', '://'.$domain.'.', config('app.BASE_URL')).'/whatsloop/webhooks/messages-webhook',
                    'ignoreOldMessages' => 1,
                ];
                $updateResult = $mainWhatsLoopObj->postSettings($myData);
                $result = $updateResult->json();

                $sendData['limit'] = 0;
                
                $diags = $mainWhatsLoopObj->dialogs($sendData);
                $diags = $diags->json();
                if(isset($diags['data']) && !empty($diags['data'])){
                    $count = count($diags['data']['dialogs']);
                    if($count > ChatDialog::count()){
                        try {
                            dispatch(new SyncDialogsJob($diags['data']['dialogs']))->onConnection('cjobs');
                        } catch (Exception $e) {
                            
                        }
                    }
                }

                $fetchData = $mainWhatsLoopObj->labelsList($sendData);
                $fetchData = $fetchData->json();

                if(isset($fetchData['data']) && !empty($fetchData['data'])){
                    $labels = $fetchData['data']['labels'];
                    $value = 1;
                    if(empty($labels)){
                        $value = 0;
                    }

                    $varObj = Variable::where('var_key','BUSINESS')->first();
                    if(!$varObj){
                        $varObj = new Variable;
                        $varObj->var_key = 'BUSINESS';
                    }
                    $varObj->var_value = $value;
                    $varObj->save();

                    $channelObj = CentralChannel::where('global_user_id',User::first()->global_id)->first();
                    foreach($labels as $label){
                        $labelObj = Category::NotDeleted()->where('labelId',$label['id'])->first();
                        if(!$labelObj){
                            $labelObj = new Category;
                            $labelObj->channel = isset($channelObj) ? $channelObj->instanceId : '';
                            $labelObj->sort = Category::newSortIndex();
                        }
                        $labelObj->labelId = $label['id'];
                        if(isset($label['name']) && !empty($label['name'])){
                            $labelObj->name_ar = $label['name'];
                            $labelObj->name_en = $label['name'];
                        }
                        $labelObj->color_id = Category::getColorData($label['hexColor'])[0];
                        $labelObj->status = 1;
                        $labelObj->save();
                    }
                }

                if(User::first()->setting_pushed == 1){
                    $meResult = $mainWhatsLoopObj->me();
                    $meResult = $meResult->json();
                    $author = $meResult['data']['id'];
                    $lastMessageObj = ChatMessage::where('fromMe',1)->orderBy('time','DESC')->first();
                    if($lastMessageObj != null && $lastMessageObj->time != null && $lastMessageObj->author == $author){
                        $sendData['min_time'] = $lastMessageObj->time - 7200;
                    }
                }
                $updateResult = $mainWhatsLoopObj->messages($sendData);
                if(isset($updateResult['data']) && !empty($updateResult['data'])){
                    $result = $updateResult->json();
                    try {
                        dispatch(new SyncMessagesJob($result['data']['messages']))->onConnection('cjobs');
                    } catch (Exception $e) {
                        
                    }
                }

                $this->emit('statusChanged'); 
            }
        }        
        return view('livewire.qr-image')->with('data',(object) $data);
    }
}
