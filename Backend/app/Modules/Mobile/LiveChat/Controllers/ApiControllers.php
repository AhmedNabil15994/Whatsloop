<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\ChatMessage;
use App\Models\ChatDialog;
use App\Models\Category;
use App\Models\Contact;
use App\Models\ContactLabel;
use App\Models\Reply;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserExtraQuota;
use App\Models\ChatEmpLog;
use App\Events\SentMessage;
use App\Events\DialogPinStatus;
use App\Events\ChatReadStatus;
use App\Events\ChatLabelStatus;
use App\Jobs\NewDialogJob;


class ApiControllers extends Controller {

    use \TraitsFunc;

    public function dialogs(Request $request) {
        $input = \Request::all();
        $data['limit'] = isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : 30;
        $data['name'] = isset($input['name']) && !empty($input['name']) ? $input['name'] : null;

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);

        $dialogs = ChatDialog::dataList($data['limit'],$data['name']);
        $dataList = $dialogs;
        if($data['name'] == null){
            $dataList['pinnedConvs'] = ChatDialog::getPinned()['data'];
        }
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        tenancy()->end($tenant);

        return \Response::json((object) $dataList);        
    }

    public function pinChat(Request $request) {
        $input = \Request::all();

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['liveChatId'] = $input['chatId'];
        $result = $mainWhatsLoopObj->pinChat($data);
        $result = $result->json();

        if($result == null){
            return \TraitsFunc::ErrorMessage('Please Contact System administrator!');
        }

        if($result['status']['status'] != 1){
            if( $result['status']['message'] != 'chat already pinned'){
                return \TraitsFunc::ErrorMessage($result['status']['message']);
            }
        }

        $domain = DOMAIN;

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);

        $dialogObj = ChatDialog::where('id',$input['chatId'])->first();
        $dialogObj->is_pinned = 1;
        $dialogObj->save();

        broadcast(new DialogPinStatus($domain, ChatDialog::getData($dialogObj) , 1 ));
        tenancy()->end($tenant);
        
        $dataList['data'] = $result['data'];
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);        
    }

    public function unpinChat(Request $request) {
        $input = \Request::all();

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['liveChatId'] = $input['chatId'];
        $result = $mainWhatsLoopObj->unpinChat($data);
        $result = $result->json();

        if($result == null){
            return \TraitsFunc::ErrorMessage('Please Contact System administrator!');
        }

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }

        $domain = DOMAIN;

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);

        $dialogObj = ChatDialog::where('id',$input['chatId'])->first();
        $dialogObj->is_pinned = 0;
        $dialogObj->save();

        broadcast(new DialogPinStatus($domain, ChatDialog::getData($dialogObj) , 0 ));
        tenancy()->end($tenant);

        $dataList['data'] = $result['data'];
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);      
    }

    public function messages(Request $request) {
        $input = \Request::all();
        if((!isset($input['chatId']) || empty($input['chatId'])) && (!isset($input['message']) || empty($input['message']))){
            return \TraitsFunc::ErrorMessage("Chat ID Field Is Required");
        }
        $data['liveChatId'] = isset($input['chatId']) && !empty($input['chatId']) ? $input['chatId'] : null;
        $data['limit'] = isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : 30;

        $is_admin = IS_ADMIN;
        $user_id = USER_ID; 

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);

        if(!$is_admin){
            $dialogObj = ChatDialog::getData(ChatDialog::getOne($input['chatId']));
            if(in_array($user_id, $dialogObj->modsArr)){
                ChatEmpLog::newLog($input['chatId']);
            }
        }

        $dataList = ChatMessage::dataList($data['liveChatId'],$data['limit']);
        
        tenancy()->end($tenant);

        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function readChat(Request $request) {
        $input = \Request::all();

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['liveChatId'] = $input['chatId'];
        $result = $mainWhatsLoopObj->readChat($data);
        $result = $result->json();

        if($result == null){
            return \TraitsFunc::ErrorMessage('Please Contact System administrator!');
        }

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }
        $domain = DOMAIN;

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);

        $dialogObj = ChatDialog::where('id',$input['chatId'])->first();
        $dialogObj->is_read = 1;
        $dialogObj->save();

        ChatMessage::where('author',$input['chatId'])->update(['sending_status' => 3]);
        broadcast(new ChatReadStatus($domain, ChatDialog::getData($dialogObj) , 1 ));
        tenancy()->end($tenant);
        
        $dataList['data'] = $result['data'];
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);   
    }

    public function unreadChat(Request $request) {
        $input = \Request::all();
        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['liveChatId'] = $input['chatId'];
        $result = $mainWhatsLoopObj->unreadChat($data);
        $result = $result->json();

        if($result == null){
            return \TraitsFunc::ErrorMessage('Please Contact System administrator!');
        }

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }
        $domain = DOMAIN;

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);

        $dialogObj = ChatDialog::where('id',$input['chatId'])->first();
        $dialogObj->is_read = 0;
        $dialogObj->save();

        ChatMessage::where('author',$input['chatId'])->update(['sending_status' => 2]);
        broadcast(new ChatReadStatus($domain, ChatDialog::getData($dialogObj) , 0 ));
        tenancy()->end($tenant);

        $dataList['data'] = $result['data'];
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);     
    }
   
    public function sendMessage(Request $request) {
        $input = \Request::all();

        $startDay = strtotime(date('Y-m-d 00:00:00'));
        $endDay = strtotime(date('Y-m-d 23:59:59'));
        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);

        $messagesCount = ChatMessage::where('fromMe',1)->where('status','!=',null)->where('time','>=',$startDay)->where('time','<=',$endDay)->count();
        $dailyCount = 10000;
        $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,1);
        if($dailyCount <= $messagesCount + $extraQuotas){
            return \TraitsFunc::ErrorMessage('Messages Quota Per Day Exceeded!!!');
        }

        if(!isset($input['type']) || empty($input['type']) ){
            return \TraitsFunc::ErrorMessage("Type Field Is Required");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Field Is Required");
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        if(!isset($input['messageType']) || $input['messageType'] != 'new'){
            $sendData['chatId'] = $input['chatId'];
            $caption = '';
            $checkData['phone'] = str_replace('@c.us', '', $input['chatId']);
            $checkResult = $mainWhatsLoopObj->checkPhone($checkData);
            $checkNoResult = $checkResult->json();

            if($checkNoResult == null){
                return \TraitsFunc::ErrorMessage('Please Contact System administrator!');
            }

            if($checkNoResult['status']['status'] != 1){
                $status = 0;
            }

            if(isset($checkNoResult['data'])){
                $status = $checkNoResult['data']['result'] == 'exists' ? 1 : 0;
            }
            if(!$status){   
                return \TraitsFunc::ErrorMessage("Chat ID Is Invalid");
            }
        }

        $domain = DOMAIN;

        if(isset($input['messageType']) && $input['messageType'] == 'new'){
            $chats = explode(',', $input['chatId']);
            unset($input['chatId']);
            foreach ($chats as $chat) {
                $checkData['phone'] = str_replace('@c.us', '', $chat);
                $checkResult = $mainWhatsLoopObj->checkPhone($checkData);
                $checkNoResult = $checkResult->json();

                if($checkNoResult == null){
                    return \TraitsFunc::ErrorMessage('Please Contact System administrator!');
                }

                if($checkNoResult['status']['status'] != 1){
                    $status = 0;
                }

                if(isset($checkNoResult['data'])){
                    $status = $checkNoResult['data']['result'] == 'exists' ? 1 : 0;
                }
                if(!$status){   
                    return \TraitsFunc::ErrorMessage($checkNoResult['status']['message']);
                }
                dispatch(new NewDialogJob( $chats , $input , $request->hasFile('file') ? $request->file('file') : null  , $domain));
            }
            $dataList['status'] = \TraitsFunc::SuccessMessage("Message Sent Successfully !.");
            return \Response::json((object) $dataList);       
        }

        if(isset($input['replyOn']) && !empty($input['replyOn'])){
            $quotedMessageObj = ChatMessage::where('id',$input['replyOn'])->first();
            if(!$quotedMessageObj){
                return \TraitsFunc::ErrorMessage('This Message Not Found to be replied');
            }
            $quotedMessageObj = ChatMessage::getData($quotedMessageObj);
            $sendData['quotedMsgId'] = $input['replyOn'];
        }

        if($input['type'] == 1){
            if(!isset($input['message']) || empty($input['message']) ){
                return \TraitsFunc::ErrorMessage("Message Field Is Required");
            }

            $message_type = 'text';
            $sendData['body'] = $input['message'];
            $bodyData = $input['message'];
            $whats_message_type = 'chat';
            $result = $mainWhatsLoopObj->sendMessage($sendData);
        }elseif($input['type'] == 2){
            if ($request->hasFile('file')) {
                $image = $request->file('file');

                $file_size = $image->getSize();
                $file_size = $file_size/(1024 * 1024);
                $file_size = number_format($file_size,2);
                $uploadedSize = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
                $totalStorage = 10000000;
                $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,3);
                if($totalStorage + $extraQuotas < (doubleval($uploadedSize) + $file_size) / 1024){
                    return \TraitsFunc::ErrorMessage(trans('main.storageQuotaError'));
                }

                $myType = explode('/', $image->getMimeType())[1];
                $message_type = \ImagesHelper::checkExtensionType($myType);

                $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image,$message_type);
                if($image == false || $fileName == false){
                    return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                }            
                $bodyData = config('app.BASE_URL').'/public/uploads/'.TENANT_ID.'/chats/'.$fileName;
                $sendData['filename'] = $fileName;
                $type = pathinfo($bodyData, PATHINFO_EXTENSION);
                $data = file_get_contents($bodyData);
                $base64 ='data: '.mime_content_type(public_path().'/uploads/'.TENANT_ID.'/chats/'.$fileName) . $type . ';base64,' . base64_encode($data);
                $sendData['body'] = $base64;    
                
                if($message_type == 'photo'){
                    if(isset($input['caption']) && !empty($input['caption']) ){
                        $sendData['caption'] = $input['caption'];
                        $caption = $input['caption'];
                    }
                }
                $whats_message_type = $message_type == 'photo' ? 'image' : 'document' ;
                $result = $mainWhatsLoopObj->sendFile($sendData);
            }
        }elseif($input['type'] == 3){
            if ($request->hasFile('file')) {
                $image = $request->file('file');

                $file_size = $image->getSize();
                $file_size = $file_size/(1024 * 1024);
                $file_size = number_format($file_size,2);
                $uploadedSize = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
                $totalStorage = 10000000;
                $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,3);
                if($totalStorage + $extraQuotas < (doubleval($uploadedSize) + $file_size) / 1024){
                    return \TraitsFunc::ErrorMessage(trans('main.storageQuotaError'));
                }

                $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                if($image == false || $fileName == false){
                    return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                }            
                $bodyData = config('app.BASE_URL').'/public/uploads/'.TENANT_ID.'/chats/'.$fileName;
                $message_type = "video";
                $sendData['filename'] = $fileName;
                $type = pathinfo($bodyData, PATHINFO_EXTENSION);
                $data = file_get_contents($bodyData);
                $base64 ='data: '.mime_content_type(public_path().'/uploads/'.TENANT_ID.'/chats/'.$fileName) . $type . ';base64,' . base64_encode($data);
                $sendData['body'] = $base64;    
                $whats_message_type = 'video';
                $result = $mainWhatsLoopObj->sendFile($sendData);
            }
        }elseif($input['type'] == 4){
            if ($request->hasFile('file')) {
                $image = $request->file('file');

                $file_size = $image->getSize();
                $file_size = $file_size/(1024 * 1024);
                $file_size = number_format($file_size,2);
                $uploadedSize = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
                $totalStorage = 10000000;
                $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,3);
                if($totalStorage + $extraQuotas < (doubleval($uploadedSize) + $file_size) / 1024){
                    return \TraitsFunc::ErrorMessage(trans('main.storageQuotaError'));
                }
                
                $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                if($image == false || $fileName == false){
                    return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                }            
                $bodyData = config('app.BASE_URL').'/public/uploads/'.TENANT_ID.'/chats/'.$fileName;
                $message_type = "sound";
                $whats_message_type = 'ppt';
                $type = pathinfo($bodyData, PATHINFO_EXTENSION);
                $data = file_get_contents($bodyData);
                $base64 ='data: '.mime_content_type(public_path().'/uploads/'.TENANT_ID.'/chats/'.$fileName) . $type . ';base64,' . base64_encode($data);
                $sendData['audio'] = $base64;    
                $result = $mainWhatsLoopObj->sendFile($sendData);
            }
            $result = $mainWhatsLoopObj->sendPTT($sendData);
        }elseif($input['type'] == 5){
            if(!isset($input['contact']) || empty($input['contact']) ){
                return \TraitsFunc::ErrorMessage("Contact Field Is Required");
            }

            $message_type = 'contact';
            $whats_message_type = 'contact';
            $sendData['contactId'] = $input['contact'];
            $bodyData = $input['contact'];
            $result = $mainWhatsLoopObj->sendContact($sendData);
        }elseif($input['type'] == 6){
            if(!isset($input['address']) || empty($input['address']) ){
                return \TraitsFunc::ErrorMessage("Address Field Is Required");
            }

            if(!isset($input['lat']) || empty($input['lat']) ){
                return \TraitsFunc::ErrorMessage("Latitude Field Is Required");
            }

            if(!isset($input['lng']) || empty($input['lng']) ){
                return \TraitsFunc::ErrorMessage("Longitude Field Is Required");
            }

            $message_type = 'location';
            $whats_message_type = 'location';
            $sendData['lat'] = $input['lat'];
            $sendData['lng'] = $input['lng'];
            $sendData['address'] = $input['address'];
            $bodyData = $input['address'];
            $result = $mainWhatsLoopObj->sendLocation($sendData);
        }elseif($input['type'] == 7){
            if(!isset($input['link']) || empty($input['link']) ){
                return \TraitsFunc::ErrorMessage("Link Field Is Required");
            }

            if(!isset($input['link_title']) || empty($input['link_title']) ){
                return \TraitsFunc::ErrorMessage("Link Title Field Is Required");
            }

            if ($request->hasFile('file')) {
                $image = $request->file('file');

                $file_size = $image->getSize();
                $file_size = $file_size/(1024 * 1024);
                $file_size = number_format($file_size,2);
                $uploadedSize = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
                $totalStorage = 10000000;
                $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,3);
                if($totalStorage + $extraQuotas < (doubleval($uploadedSize) + $file_size) / 1024){
                    return \TraitsFunc::ErrorMessage(trans('main.storageQuotaError'));
                }

                $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                if($image == false || $fileName == false){
                    return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                }            
                $fullUrl = config('app.BASE_URL').'/public/uploads/'.TENANT_ID.'/chats/'.$fileName;
            }

            $message_type = 'link';
            $whats_message_type = 'link';
            $sendData['body'] = $input['link'];
            $sendData['title'] = $input['link_title'];
            $sendData['description'] = $input['link_description'];
            $bodyData = $sendData['body'];
            if(isset($fileName) && $fileName != false){
                $sendData['previewBase64'] = base64_encode(file_get_contents($fullUrl));
            }
            $result = $mainWhatsLoopObj->sendLink($sendData);
        }

        $result = $result->json();
        if($result == null){
            return \TraitsFunc::ErrorMessage('Please Contact System administrator!');
        }

        if(isset($result['data']) && isset($result['data']['id'])){
            $checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
            $messageId = $result['data']['id'];
            $lastMessage['status'] = 'APP';
            $lastMessage['id'] = $messageId;
            $lastMessage['fromMe'] = 1;
            $lastMessage['chatId'] = $sendData['chatId'];
            $lastMessage['time'] = date('Y-m-d H:i:s');
            $lastMessage['body'] = $bodyData;
            $lastMessage['caption'] = $caption;
            $lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
            $lastMessage['message_type'] = $message_type;
            $lastMessage['sending_status'] = 1;
            $lastMessage['type'] = $whats_message_type;
            if(isset($quotedMessageObj)){
                $lastMessage['quotedMsgId'] = $input['replyOn'];
                $lastMessage['quotedMsgBody'] = $quotedMessageObj->body;
                $lastMessage['quotedMsgType'] = $quotedMessageObj->whatsAppMessageType;
            }
            if(isset($input['frontId']) && !empty($input['frontId'])){   
                $lastMessage['frontId'] = $input['frontId'];
            }
            $messageObj = ChatMessage::newMessage($lastMessage);
            $dialog = ChatDialog::getOne($sendData['chatId']);
            $dialog->last_time = strtotime($lastMessage['time']);
            $dialogObj = ChatDialog::getData($dialog);
            broadcast(new SentMessage($domain , $dialogObj ));
        
            $is_admin = IS_ADMIN;
            $user_id = USER_ID; 
            if(!$is_admin){
                $dialogObj = ChatDialog::getData(ChatDialog::getOne($input['chatId']));
                if(in_array($user_id, $dialogObj->modsArr)){
                    ChatEmpLog::newLog($input['chatId'],3);
                }
            }
        }else{
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }

        $dataList['data'] = ChatMessage::getData($messageObj);
        tenancy()->end($tenant);

        $dataList['status'] = \TraitsFunc::SuccessMessage("Message Sent Successfully !.");
        return \Response::json((object) $dataList);        
    }

    public function liveChatLogout(){
        $is_admin = IS_ADMIN;
        $user_id = USER_ID;

        if(!$is_admin){
            $tenant = Tenant::find(TENANT_ID);      
            tenancy()->initialize($tenant);
            
            $lastObj = ChatEmpLog::where('user_id',$user_id)->where('type','!=',3)->orderBy('id','DESC')->first();
            if($lastObj != null && $lastObj->ended == 0 && $lastObj->type == 1){
                $lastObj->ended = 1;
                $lastObj->save();
                ChatEmpLog::newRecord($lastObj->chatId,2,$user_id,date('Y-m-d H:i:s'),1);
            }
            tenancy()->end($tenant);
        }
        return redirect()->to('/dashboard');
    }

    public function labels(Request $request) {
        $input = \Request::all();
        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);
        $dataList = Category::dataList();
        tenancy()->end($tenant);
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function labelChat(Request $request) {
        $input = \Request::all();

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        if(!isset($input['labelId']) || empty($input['labelId']) ){
            return \TraitsFunc::ErrorMessage("Label ID Is Required");
        }

        if(!isset($input['labelId']) || empty($input['labelId']) ){
            return \TraitsFunc::ErrorMessage("Label ID Is Required");
        }

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);
        $categoryObj = Category::NotDeleted()->where('labelId',$input['labelId'])->first();
        tenancy()->end($tenant);
        if(!$categoryObj){
            return \TraitsFunc::ErrorMessage("Label Not Found");
        }


        $data['liveChatId'] = $input['chatId'];
        $data['labelId'] = $input['labelId'];
        
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $result = $mainWhatsLoopObj->labelChat($data);
        $result = $result->json();

        if($result == null){
            return \TraitsFunc::ErrorMessage('Please Contact System administrator!');
        }

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }
        // dd($input);

        $domain = DOMAIN;

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);
        $contactLabelObj = ContactLabel::newRecord(str_replace('@c.us', '', $input['chatId']),$input['labelId']);
       
        broadcast(new ChatLabelStatus($domain, ChatDialog::getData(ChatDialog::getOne($input['chatId'])) , Category::getData($categoryObj) , 1 ));
        tenancy()->end($tenant);
        
        $dataList['data'] = $result['data'];
        $dataList['status'] =  $result['status'];
        return \Response::json((object) $dataList);  
    }

    public function unlabelChat(Request $request) {
        $input = \Request::all();

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        if(!isset($input['labelId']) || empty($input['labelId']) ){
            return \TraitsFunc::ErrorMessage("Label ID Is Required");
        }

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);
        $categoryObj = Category::NotDeleted()->where('labelId',$input['labelId'])->first();
        if(!$categoryObj){
            return \TraitsFunc::ErrorMessage("Label Not Found");
        }
        tenancy()->end($tenant);

        $data['liveChatId'] = $input['chatId'];
        $data['labelId'] = $input['labelId'];
        
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $result = $mainWhatsLoopObj->unlabelChat($data);
        $result = $result->json();

        if($result == null){
            return \TraitsFunc::ErrorMessage('Please Contact System administrator!');
        }

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }

        $domain = DOMAIN;

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);
        ContactLabel::where('contact',str_replace('@c.us', '', $input['chatId']))->where('category_id',$input['labelId'])->delete();
        broadcast(new ChatLabelStatus($domain, ChatDialog::getData(ChatDialog::getOne($input['chatId'])) , Category::getData($categoryObj) , 0 ));
        tenancy()->end($tenant);
        
        $dataList['data'] = $result['data'];
        $dataList['status'] =  $result['status'];
        return \Response::json((object) $dataList);    
    }

    public function contact(Request $request) {
        $input = \Request::all();
        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }   

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);
        $chatObj = ChatDialog::getOne($input['chatId']);
        if($chatObj == null){
            return \TraitsFunc::ErrorMessage("Dialog Is Missing");
        }

        $contactObj = Contact::NotDeleted()->where('phone','+'.str_replace('@c.us', '', $input['chatId']))->first();
        $contact_details = [];
        if($contactObj != null){
            $contact_details = Contact::getData($contactObj,null,null,true);
        }        

        $dataObj = ChatDialog::getData($chatObj,true);
        $dataObj->contact_details = $contact_details;
        $dataList['data'] = $dataObj;
        tenancy()->end($tenant);

        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);      
    }

    public function updateContact(Request $request) {
        $input = \Request::all();

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }
        
        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);
        $contactObj =  Contact::NotDeleted()->where('phone','+'.str_replace('@c.us', '', $input['chatId']))->first();
        
        if(!$contactObj){
            return \TraitsFunc::ErrorMessage("Invalid Contact");
        }

        if(isset($input['name']) && !empty($input['name'])){
            $contactObj->name = $input['name'];
        }
        if(isset($input['email']) && !empty($input['email'])){
            $contactObj->email = $input['email'];
        }
        if(isset($input['city']) && !empty($input['city'])){
            $contactObj->city = $input['city'];
        }
        if(isset($input['country']) && !empty($input['country'])){
            $contactObj->country = $input['country'];
        }
        if(isset($input['notes']) && !empty($input['notes'])){
            $contactObj->notes = $input['notes'];
        }
        if(isset($input['lang']) && !empty($input['lang']) && in_array($input['lang'], [0,1])){
            $contactObj->lang = $input['lang'];
        }
        $contactObj->save();
        if(isset($input['name']) && !empty($input['name'])){
            $chatObj = ChatDialog::where('id',$input['chatId'])->first();
            $chatObj->name = $input['name'];
            $chatObj->save();
        }
        tenancy()->end($tenant);
        
        $dataList['status'] = \TraitsFunc::SuccessMessage('Data Updated Successfully.');
        return \Response::json((object) $dataList);      
    }

    public function quickReplies(Request $request) {
        $input = \Request::all();
        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);
        $dataList = Reply::dataList();
        tenancy()->end($tenant);
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function moderators(Request $request) {
        $input = \Request::all();
        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);
        $dataList = User::dataList(2);
        tenancy()->end($tenant);
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function assignMod(Request $request) {
        $input = \Request::all();

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }
        if(!isset($input['modId']) || empty($input['modId']) ){
            return \TraitsFunc::ErrorMessage("Moderator ID Is Required");
        }

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);

        $modObj = User::getOne($input['modId']);
        if($modObj == null || $modObj->group_id != 2){
            return \TraitsFunc::ErrorMessage('Invalid Moderator');
        }

        $dialogObj = ChatDialog::getOne($input['chatId']);
        $modArrs = $dialogObj->modsArr;
        if($modArrs == null){
            $dialogObj->modsArr = serialize([$input['modId']]);
            $dialogObj->save();
        }else{
            $oldArr = unserialize($dialogObj->modsArr);
            if(!in_array($input['modId'], $oldArr)){
                array_push($oldArr, $input['modId']);
                $dialogObj->modsArr = serialize($oldArr);
                $dialogObj->save();
            }
        }
        tenancy()->end($tenant);

        $dataList['status'] = \TraitsFunc::SuccessMessage("Moderator Added To Conversation Successfully.");
        return \Response::json((object) $dataList);        
    }

    public function removeMod(Request $request) {
        $input = \Request::all();

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }
        if(!isset($input['modId']) || empty($input['modId']) ){
            return \TraitsFunc::ErrorMessage("Moderator ID Is Required");
        }

        $tenant = Tenant::find(TENANT_ID);      
        tenancy()->initialize($tenant);

        $modObj = User::getOne($input['modId']);
        if($modObj == null || $modObj->group_id != 2){
            return \TraitsFunc::ErrorMessage('Invalid Moderator');
        }

        $dialogObj = ChatDialog::getOne($input['chatId']);
        $modArrs = $dialogObj->modsArr;
        if($modArrs == null){
            return \TraitsFunc::ErrorMessage("Moderator Not Belonged To This Conversation");
        }else{
            $oldArr = unserialize($dialogObj->modsArr);
            if(!in_array($input['modId'], $oldArr)){
                return \TraitsFunc::ErrorMessage("Moderator Not Belonged To This Conversation");
            }else{
                if (($key = array_search($input['modId'], $oldArr)) !== false) {
                    unset($oldArr[$key]);
                }
                $dialogObj->modsArr = serialize($oldArr);
                $dialogObj->save();
            }
        }
        tenancy()->end($tenant);
        $dataList['status'] = \TraitsFunc::SuccessMessage("Moderator Removed From Conversation Successfully.");
        return \Response::json((object) $dataList);        
    }
}
