<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Http;
use URL;

class WhatsloopControllers extends Controller {

    use \TraitsFunc;

    /*----------------------------------------------------------
    Instances
    ----------------------------------------------------------*/
    public function status(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'status';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken]);
        if($result->ok()){
            $result = $result->json();
            if(isset($result['qrCode']) && !empty($result['qrCode'])){
                $image = '/uploads/qrCode' . time() . '.jpg';
                $destinationPath = public_path() . $image;
                $succ = file_put_contents($destinationPath, base64_decode($result['qrCode']));     
            }
            $statusObj['data'] = URL::to($image);
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj);
    }

    public function qr_code(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'qr_code';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken]);
        if($result->ok()){
            $image = '/uploads/qrCode' . time() . '.jpg';
            $destinationPath = public_path() . $image;
            $succ = file_put_contents($destinationPath, $result);
            $statusObj['data'] = URL::to($image);
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function logout(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'logout';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function takeover(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'takeover';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL);
        if($result->ok()){
            $statusObj['data'] = $result->json()['result'];
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function expiry(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'expiry';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL);
        if($result->ok()){
            $statusObj['data'] = $result->json()['result'];
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function retry(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'retry';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL);
        if($result->ok()){
            $statusObj['data'] = $result->json()['result'];
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function reboot(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'reboot';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL);
        if($result->ok()){
            $statusObj['data'] = $result->json()['result'];
        }else{
            return \TraitsFunc::ErrorMessage($result->json()['message'], 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function settings(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'settings';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function postSettings(){
        $channelID = 236484;
        $input = \Request::all();
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'lnawjm3auirc8j64';

        $method = 'settings';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,$input);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function outputIP(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'outputIP';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function me(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'me';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function setName(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'setName';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,['pushname' => @$input['name']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function setStatus(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'setStatus';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,['status' => @$input['status']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function repeatHook(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'repeatHook';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,['messageId   ' => @$input['messageId']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function labelsList(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'labelsList';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function createLabel(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'createLabel';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,['name' => @$input['name']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function updateLabel(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'updateLabel';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,['name' => @$input['name'] , 'color' => @$input['color'] , 'labelId' => @$input['labelId']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function removeLabel(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'removeLabel';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,['labelId' => @$input['labelId']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    /*----------------------------------------------------------
    Messages
    ----------------------------------------------------------*/

    public function sendMessage(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'sendMessage';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,['body' => @$input['body'],'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function sendFile(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'sendFile';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['body'] = URL::to('/').'/uploads/qrCode1614973212.jpg';
        $input['filename'] = 'qrCode1614973212.jpg';
        $result = Http::post($fullURL,['body' => @$input['body'],'filename'=>@$input['filename'],'caption'=>@$input['caption'],'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function sendPTT(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'sendPTT';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['audio'] = URL::to('/').'/uploads/qrCode1614973212.jpg';
        $result = Http::post($fullURL,['audio' => @$input['audio'],'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function sendLink(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'sendLink';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['body'] = URL::to('/');
        $input['title'] = 'qrCode1614973212.jpg';
        $input['previewBase64'] = base64_encode(file_get_contents(URL::to('/').'/uploads/qrCode1614973212.jpg'));

        $result = Http::post($fullURL,['body' => @$input['body'],'title'=>@$input['title'],'description'=>@$input['description'],'previewBase64'=>@$input['previewBase64'],'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function sendContact(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'sendContact';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['contactId'] = [$input['phone'].'@c.us'];
        $result = Http::post($fullURL,['contactId' => @$input['contactId'],'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function sendLocation(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'sendLocation';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['lat' => @$input['lat'],'lng'=>@$input['lng'],'address'=>@$input['address'],'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function sendVCard(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'sendVCard';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['vcard' => @$input['vcard'],'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function forwardMessage(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'forwardMessage';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['messageId'] = [$input['messageId']];
        $result = Http::post($fullURL,['messageId' => @$input['messageId'],'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function messages(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'messages';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken,'chatId' => @$input['chatId'] , 'limit' => @$input['limit'] ]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function messagesHistory(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'messagesHistory';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken,'chatId' => @$input['chatId'] , 'page' => @$input['page'] , 'count' => @$input['count'] ]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function deleteMessage(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'deleteMessage';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['messageId'] = $input['messageId'];
        $result = Http::post($fullURL,['messageId' => @$input['messageId']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    /*----------------------------------------------------------
    Dialogs
    ----------------------------------------------------------*/

    public function dialogs(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'dialogs';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken , 'limit' => @$input['limit'] ]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function dialog(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'dialog';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken , 'chatId' => @$input['chatId'] ]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function group(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'group';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['phones'] = [@$input['phones']];
        $result = Http::post($fullURL,['groupName' => @$input['groupName'],'phones' => $input['phones']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function pinChat(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'pinChat';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['phone'] = [@$input['phone']];
        $result = Http::post($fullURL,['phone' => $input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function unpinChat(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'unpinChat';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['phone'] = [@$input['phone']];
        $result = Http::post($fullURL,['phone' => $input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function readChat(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'readChat';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['phone'] = [@$input['phone']];
        $result = Http::post($fullURL,['phone' => $input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function unreadChat(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'unreadChat';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['phone'] = [@$input['phone']];
        $result = Http::post($fullURL,['phone' => $input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function removeChat(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'removeChat';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['chatId' => @$input['chatId']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function joinGroup(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'joinGroup';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['code' => @$input['code']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function leaveGroup(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'leaveGroup';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['chatId'=>@$input['chatId']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function addGroupParticipant(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'addGroupParticipant';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['groupId'=>@$input['groupId'],'participantPhone'=>@$input['participantPhone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function removeGroupParticipant(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'removeGroupParticipant';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['groupId'=>@$input['groupId'],'participantPhone'=>@$input['participantPhone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function promoteGroupParticipant(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'promoteGroupParticipant';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['groupId'=>@$input['groupId'],'participantPhone'=>@$input['participantPhone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function demoteGroupParticipant(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'demoteGroupParticipant';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['groupId'=>@$input['groupId'],'participantPhone'=>@$input['participantPhone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function typing(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'typing';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['on'] = isset($input['on']) ? 'start' : 'stop';
        $input['duration'] = isset($input['duration']) ? $input['duration'] : '5';
        $result = Http::post($fullURL,['chatId'=>@$input['chatId'] ,'on' => $input['on'] , 'duration' => $input['duration'] ]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function recording(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'recording';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $input['on'] = isset($input['on']) ? 'start' : 'stop';
        $input['duration'] = isset($input['duration']) ? $input['duration'] : '5';
        $result = Http::post($fullURL,['chatId'=>@$input['chatId'] ,'on' => $input['on'] , 'duration' => $input['duration'] ]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function labelChat(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'labelChat';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['chatId'=>@$input['chatId'] ,'labelId' => $input['labelId'] ]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function unlabelChat(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'unlabelChat';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $result = Http::post($fullURL,['chatId'=>@$input['chatId'] ,'labelId' => $input['labelId'] ]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    /*----------------------------------------------------------
    Webhooks
    ----------------------------------------------------------*/

    public function webhook(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'webhook';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $params = ['webhookUrl' => @$input['webhookUrl']];
        $result = Http::post($fullURL,$params);
        if($result->ok()){
            $statusObj['data'] = $result->json()['webhookUrl'];
        }else{
            return \TraitsFunc::ErrorMessage($result->json()['message'], 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    /*----------------------------------------------------------
    Queues
    ----------------------------------------------------------*/

    public function showMessagesQueue(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'showMessagesQueue';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function clearMessagesQueue(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'clearMessagesQueue';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $params = [];
        $result = Http::post($fullURL,$params);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function showActionsQueue(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'showActionsQueue';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function clearActionsQueue(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'clearActionsQueue';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        $params = [];
        $result = Http::post($fullURL,$params);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    /*----------------------------------------------------------
    Ban
    ----------------------------------------------------------*/

    public function banSettings(){
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'banSettings';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function postBanSettings(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'banSettings';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,['banPhoneMask' => @$input['banPhoneMask'] , 'preBanMessage' => @$input['preBanMessage'] , 'set' => @$input['set']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function banTest(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'banTest';
        $fullURL = $mainURL.$method.'?token='.$channelToken;
        
        $result = Http::post($fullURL,['phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    /*----------------------------------------------------------
    Testing
    ----------------------------------------------------------*/

    public function instanceStatuses(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'instanceStatuses';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken,'min_time'=> @$input['min_time'] , 'max_time' => @$input['max_time']]);
        if($result->ok()){
            $statusObj['data'] = $result->json()['data'];
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function webhookStatus(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'webhookStatus';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken,'msgId' => @$input['msgId']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    public function checkPhone(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'checkPhone';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken , 'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }

    /*----------------------------------------------------------
    Users
    ----------------------------------------------------------*/

    public function userStatus(){
        $input = \Request::all();
        $channelID = 235335;
        $mainURL = 'https://api.chat-api.com/instance'.$channelID.'/';
        $channelToken = 'p2lm6ma1ujm9wifn';

        $method = 'userStatus';
        $fullURL = $mainURL.$method;
        
        $result = Http::get($fullURL,['token'=> $channelToken,'chatId' => @$input['chatId'] , 'phone' => @$input['phone']]);
        if($result->ok()){
            $statusObj['data'] = $result->json();
        }else{
            return \TraitsFunc::ErrorMessage("Server Error", 400);
        }
        
        $statusObj['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $statusObj); 
    }
}
