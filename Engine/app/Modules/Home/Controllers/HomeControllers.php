<?php namespace App\Http\Controllers;

use Request;
use Response;
use URL;
use App\Models\Variable;
use Illuminate\Support\Facades\Http;

class HomeControllers extends Controller {

    use \TraitsFunc;

    public function formatResponse($serverResult,$status=null){
    	if(!$serverResult->ok()){
        	$result = $serverResult->json();
            return [0,$result['error']];
        }else{
        	$result = $serverResult->json();
        	if(isset($result['error']) && !empty($result['error'])){
            	return [0,$result['error']];
        	}
        	if(isset($result['result']) && $result['result'] == 'failed'){
            	return [0,str_replace('@c.us', '', $result['message'])];
        	}
            if(is_array($result) && !in_array($status, ['labelsList','showMessagesQueue','showActionsQueue','allMessages','messagesHistory'])){
                $extraResult = array_values($result);
                if(isset($extraResult[0]) && $extraResult[0] == false && !isset($result['sendDelay'])){
                    return [0,str_replace('@c.us', '', @$result['message'])];
                }                
            }

        	if(isset($result['result']) && $result['result'] == "Couldn't delete chat or leaving group. Invalid number"){
            	return [0,"Couldn't delete chat or leaving group. Invalid number"];
        	}
        	return [1,'success'];
        }
    }

    public function index($status) {
    	$input = Request::all();

    	// Customization For Specific Routes
    	if(isset($input['contactId']) && !empty($input['contactId']) && $status == 'sendContact' ){
    		if(!is_array(json_decode($input['contactId']))){
    			$input['contactId'] = $input['contactId'].'@c.us';
    		}else{
    			$contactIds=[];
    			foreach (json_decode($input['contactId']) as $value) {
    				$contactIds[]=$value.'@c.us';
    			}
    			$input['contactIds'] = $contactIds;
    		}
    	}

        if(isset($input['phones']) && !empty($input['phones']) && $status == 'group' ){
            $phones=[];
            foreach (json_decode($input['phones']) as $value) {
                $phones[]=$value.'@c.us';
            }
            $input['chatIds'] = $phones;
        }

    	if(isset($input['messageId']) && !empty($input['messageId']) && in_array($status, ['forwardMessage'])){
    		if(!is_array($input['messageId'])){
    			$input['messageId'] = [$input['messageId']];
    		}
    	}

    	if(in_array($status, ['dialogs','pinChat','unpinChat','readChat','unreadChat','removeChat','leaveGroup','typing','recording','labelChat','unlabelChat','dialog','allMessages','messagesHistory'])){
    		if(isset($input['chatId']) && !empty($input['chatId'])){
    			$input['chatId'] = $input['chatId'].'@c.us';
    		}

    		if(isset($input['groupId']) && !empty($input['groupId'])){
    			$input['chatId'] = $input['groupId'].'@g.us';
    			unset($input['groupId']);
    		}
    	}

    	if(in_array($status, ['addGroupParticipant','removeGroupParticipant','promoteGroupParticipant','demoteGroupParticipant'])){
    		if(isset($input['participantPhone']) && !empty($input['participantPhone'])){
    			$input['participantChatId'] = $input['participantPhone'].'@c.us';
    		}

    		if(isset($input['groupId']) && !empty($input['groupId'])){
    			$input['groupId'] = $input['groupId'].'@g.us';
    		}
    	}

    	if(isset($input['phone']) && !empty($input['phone']) && !in_array($status, ['banTest','checkPhone'])){
    		if(!is_array($input['phone'])){
    			$input['chatId'] = $input['phone'].'@c.us';
    			unset($input['phone']);
    		}
    	}

        if(isset($input['liveChatId']) && !empty($input['liveChatId']) && in_array($status, ['dialog','pinChat','unpinChat','readChat','unreadChat','typing','recording','allMessages','dialogs','labelChat','unlabelChat'])){
            if(!is_array($input['liveChatId'])){
                $input['chatId'] = $input['liveChatId'];
                unset($input['liveChatId']);
            }
        }

    	// Whatsapp Integration
    	$whatsLoopObj =  new \MainWhatsLoop();
        $serverResult = $whatsLoopObj->$status($input);

        $formatResponeResult = $this->formatResponse($serverResult,$status);
        if($formatResponeResult[0] == 0){
        	return \TraitsFunc::ErrorMessage($formatResponeResult[1]);
        }



        $dataList['data'] = $serverResult->json();
        // Customization For QR Code Images
        if(in_array($status, ['status','qr_code','screenshot'])){
        	$image = '/uploads/instanceImage' . time() . '.png';
            $destinationPath = public_path() . $image;
            if($status == 'status'){
            	$result = $serverResult->json();
                if(isset($result['qrCode'])){
                    $qrCode =  base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $result['qrCode']));
                    $succ = file_put_contents($destinationPath, $qrCode);   
                }
            }else{
	            $succ = file_put_contents($destinationPath, $serverResult);
            }
	        $dataList['data']['image'] = URL::to($image);
        }

        // Customization All Messages & Messages History
        if(in_array($status, ['allMessages','messagesHistory'])){
            $messagesObj = $dataList['data']['messages'];
            $messagesArr = [];
            foreach ($messagesObj as $key => $message) {
                if(in_array($message['type'], ['image','ppt','video','document'])){
                    $folder = '/uploads/messages/'.CHANNEL_ID.'/'.$message['id'];
                    $url = $message['body'];
                    $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                    $directory = public_path().$folder;
                    $image = $directory.'/chatFile.'.$extension;
                    if(!file_exists($directory)){
                        @$content = file_get_contents($url);                    
                        mkdir($directory, 0777, true);
                        $succ = file_put_contents($image, $content);   
                    }
                    $message['body'] = URL::to('/').'/public'.$folder.'/chatFile.'.$extension; 
                }
                $message['time'] = date('Y-m-d H:i:s',$message['time']);
                $messagesArr[] = $message;
            }
            $dataList['data']['messages'] = $messagesArr;
        }



        $dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);        
    }

    public function createChannel(){
        $input['type'] = 'whatsapp';
        $input['uid'] = Variable::getVar("API_KEY");
        $baseUrl = Variable::getVar("INSTANCES_URL");
        $fullURL = $baseUrl.'newInstance';
        $serverResult = Http::post($fullURL,$input);

        $formatResponeResult = $this->formatResponse($serverResult);
        if($formatResponeResult[0] == 0){
            return \TraitsFunc::ErrorMessage($formatResponeResult[1]);
        }

        $dataList['data']['channel'] = $serverResult->json()['result']['instance'];
        $dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);        
    }

    public function deleteChannel(){
        $input = Request::all();
        $input['instanceId'] = $input['channelId'];
        $input['uid'] = Variable::getVar("API_KEY");
        $baseUrl = Variable::getVar("INSTANCES_URL");
        $fullURL = $baseUrl.'deleteInstance';
        $serverResult = Http::post($fullURL,$input);

        $formatResponeResult = $this->formatResponse($serverResult);
        if($formatResponeResult[0] == 0){
            return \TraitsFunc::ErrorMessage($formatResponeResult[1]);
        }

        $dataList['data'] = $serverResult->json();
        $dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);        
    }

    public function transferDays(){
        $input = Request::all();
        $input['uid'] = Variable::getVar("API_KEY");
        $baseUrl = Variable::getVar("INSTANCES_URL");
        $fullURL = $baseUrl.'transferDays';
        $serverResult = Http::post($fullURL,$input);

        $formatResponeResult = $this->formatResponse($serverResult);
        if($formatResponeResult[0] == 0){
            return \TraitsFunc::ErrorMessage($formatResponeResult[1]);
        }

        $dataList['data'] = $serverResult->json();
        $dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);      
    }

    public function channels(){
        $input['uid'] = Variable::getVar("API_KEY");
        $baseUrl = Variable::getVar("INSTANCES_URL");
        $fullURL = $baseUrl.'listInstances';
        $serverResult = Http::post($fullURL,$input);

        $formatResponeResult = $this->formatResponse($serverResult);
        if($formatResponeResult[0] == 0){
            return \TraitsFunc::ErrorMessage($formatResponeResult[1]);
        }

        $dataList['data']['channels'] = $serverResult->json()['result'];
        $dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);        
    }

}
