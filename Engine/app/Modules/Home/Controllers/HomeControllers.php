<?php namespace App\Http\Controllers;

use Request;
use Response;
use URL;

class HomeControllers extends Controller {

    use \TraitsFunc;

    public function formatResponse($serverResult){
    	if(!$serverResult->ok()){
        	$result = $serverResult->json();
            return [0,$result['error']];
        }else{
        	$result = $serverResult->json();
        	if(isset($result['error']) && !empty($result['error'])){
            	return [0,$result['error']];
        	}
        	if(isset($result['result']) && $result['result'] == 'failed'){
            	return [0,$result['message']];
        	}
            if(is_array($result)){
                $extraResult = array_values($result);
                if(isset($extraResult[0]) && $extraResult[0] == false){
                    return [0,@$result['message']];
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
    		if(!is_array($input['contactId'])){
    			$input['contactId'] = $input['contactId'].'@c.us';
    		}else{
    			$contactIds=[];
    			foreach ($input['contactId'] as $value) {
    				$contactIds[]=$value.'@c.us';
    			}
    			$input['contactIds'] = $contactIds;
    		}
    	}

        if(isset($input['phones']) && !empty($input['phones']) && $status == 'group' ){
            $phones=[];
            foreach ($input['phones'] as $value) {
                $phones[]=$value.'@c.us';
            }
            $input['chatIds'] = $phones;
        }

    	if(isset($input['messageId']) && !empty($input['messageId']) && in_array($status, ['forwardMessage'])){
    		if(!is_array($input['messageId'])){
    			$input['messageId'] = [$input['messageId']];
    		}
    	}

    	if(in_array($status, ['dialog','pinChat','unpinChat','readChat','unreadChat','removeChat','leaveGroup','typing','recording','labelChat','unlabelChat','dialog','allMessages','messagesHistory'])){
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

    	// Whatsapp Integration
    	$whatsLoopObj =  new \MainWhatsLoop();
        $serverResult = $whatsLoopObj->$status($input);

        $formatResponeResult = $this->formatResponse($serverResult);
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
        $dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);        
    }
}
