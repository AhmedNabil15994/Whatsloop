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
use App\Models\UserAddon;
use App\Models\User;
use App\Models\UserExtraQuota;
use App\Models\Variable;
use App\Models\ChatEmpLog;
use App\Models\UserChannels;
use App\Events\SentMessage;
use App\Events\DialogPinStatus;
use App\Events\ChatReadStatus;
use App\Events\ChatLabelStatus;
use App\Jobs\NewDialogJob;


class LiveChatControllers extends Controller {

    use \TraitsFunc;

    public function checkPerm(){
        $disabled = UserAddon::getDeactivated(User::first()->id);
        $dis = 0;
        if(in_array(2,$disabled)){
            $dis = 1;
        }
        return $dis;
    }

    public function index(){
        return view('Tenancy.LiveChat.Views.index');
    }

    public function dialogs(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }
        
        if((!isset($input['mine']) || empty($input['mine'])) && !\Helper::checkRules('list-livechat')){
            $dataList['data'] = 'disabled';
            $dataList['status'] = \TraitsFunc::SuccessMessage();
            return \Response::json((object) $dataList);        
        }
        
        $data['limit'] = isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : 30;
        $data['name'] = isset($input['name']) && !empty($input['name']) ? $input['name'] : null;

        // if(!IS_ADMIN && !\Helper::checkRules('list-dialogs')){
        //     $request->merge(['mine' => "3"]);
        // }

        $dialogs = ChatDialog::dataList($data['limit'],$data['name']);
 
        $dataList = $dialogs;
        if($data['name'] == null /*&& IS_ADMIN*/){
            $dataList['pinnedConvs'] = ChatDialog::getPinned()['data'];
        }
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function repeatHoook(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!isset($input['message_id']) || empty($input['message_id']) ){
            return \TraitsFunc::ErrorMessage("Message ID Is Required");
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['messageId'] = $input['message_id'];
        $result = $mainWhatsLoopObj->repeatHook($data);
        $result = $result->json();
        
        $dataList['data'] = isset($result['data']) ? $result['data'] : '';
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);        
    }

    public function deleteMessage(Request $request) {
        $input = \Request::all();
        
        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!isset($input['message_id']) || empty($input['message_id']) ){
            return \TraitsFunc::ErrorMessage("Message ID Is Required");
        }
        $messageObj = ChatMessage::where('id',$input['message_id'])->first();
        if(!$messageObj){
            return \TraitsFunc::ErrorMessage("Message is Not Found");
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['messageId'] = $input['message_id'];
        $result = $mainWhatsLoopObj->deleteMessage($data);
        $result = $result->json();
        if(($result['status']['status'] == 1 && (isset($result['data']['message']) && $result['data']['message'] != "couldn`t delete message")) || $messageObj->fromMe == 0 ){
            $messageObj->update([
                'deleted_by' => USER_ID,
                'deleted_at' => DATE_TIME,
            ]);
            $dataList['status'] = $result['status'];
        }else{
            $dataList['status']['message'] = $result['data']['message'];
        }

        $dataList['data'] = ChatMessage::getData($messageObj);
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
        if(!$is_admin){
            $dialogObj = ChatDialog::getData(ChatDialog::getOne($input['chatId']));
            ChatEmpLog::newLog($input['chatId']);
        }

        if(isset($input['message_id']) && !empty($input['message_id'])){
            $lastMessage = ChatMessage::orderBy('messageNumber','DESC')->first();
            $replyMessage = ChatMessage::where('id',$input['message_id'])->first();
            if(!$lastMessage || !$replyMessage){
                return \TraitsFunc::ErrorMessage("Invalid Message or Message ID");
            }
            $dataList = ChatMessage::generateObj(ChatMessage::where('chatId',$input['chatId'])->where('messageNumber','>=',$replyMessage->messageNumber)->orderBy('messageNumber','DESC') , $lastMessage->messageNumber - $replyMessage->messageNumber - 3);
            $dataList['status'] = \TraitsFunc::SuccessMessage();
            return \Response::json((object) $dataList);
        }

        $dataList = ChatMessage::dataList($data['liveChatId'],$data['limit']);
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);
    }

    public function sendMessage(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        $startDay = strtotime(date('Y-m-d 00:00:00'));
        $endDay = strtotime(date('Y-m-d 23:59:59'));
        $messagesCount = ChatMessage::where('fromMe',1)->where('status','!=',null)->where('time','>=',$startDay)->where('time','<=',$endDay)->count();
        $dailyCount = Session::get('dailyMessageCount');
        $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,1);
        if($dailyCount + $extraQuotas <= $messagesCount){
            return \TraitsFunc::ErrorMessage('Messages Quota Per Day Exceeded!!!');
        }

        if(!isset($input['type']) || empty($input['type']) ){
            return \TraitsFunc::ErrorMessage("Type Field Is Required");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Field Is Required");
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $domain = explode('.', $request->getHost())[0];

        // $senderStatus = ;//trans('main.channel'). ' #'.Session::get('channelCode');
        // if(!IS_ADMIN){
        //     $senderStatus = FULL_NAME;
        // }
        $senderStatus = ucwords(FULL_NAME);

        if(isset($input['messageType']) && $input['messageType'] == 'new'){
            $chats = explode(',', $input['chatId']);
            unset($input['chatId']);
            // foreach ($chats as $chat) {
            //     $checkData['phone'] = str_replace('@c.us', '', $chat);
            //     $checkResult = $mainWhatsLoopObj->checkPhone($checkData);
            //     $checkNoResult = $checkResult->json();

            //     if($checkNoResult['status']['status'] != 1){
            //         $status = 0;
            //     }

            //     if(isset($checkNoResult['data'])){
            //         $status = $checkNoResult['data']['result'] == 'exists' ? 1 : 0;
            //     }
            //     if(!$status){   
            //         return \TraitsFunc::ErrorMessage("Chat ID Is Invalid");
            //     }
                
            // }
            try {
                dispatch(new NewDialogJob( $chats , $input , $request->hasFile('file') ? $request->file('file') : null  , $domain,$senderStatus))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }
            $dataList['status'] = \TraitsFunc::SuccessMessage("Message Sent Successfully !.");
            return \Response::json((object) $dataList);       
        }

        $sendData['chatId'] = $input['chatId'];
        $caption = '';

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
            if(strpos($sendData['body'], 'http') !== false){
                $message_type = 'link';
                $whats_message_type = 'link';
                $sendData['body'] = $input['message'];
                $sendData['title'] = $input['message'];
                $sendData['description'] = $input['message'];
                $bodyData = $sendData['body'];
                $result = $mainWhatsLoopObj->sendLink($sendData);
            }else{
                $result = $mainWhatsLoopObj->sendMessage($sendData);
            }
        }elseif($input['type'] == 2){
            if ($request->hasFile('file')) {
                $image = $request->file('file');

                $file_size = $image->getSize();
                $file_size = $file_size/(1024 * 1024);
                $file_size = number_format($file_size,2);
                $uploadedSize = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
                $totalStorage = Session::get('storageSize');
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
                $sendData['body'] = $bodyData;
                if($message_type == 'photo'){
                    if(isset($input['caption']) && !empty($input['caption']) ){
                        $sendData['caption'] = $input['caption'];
                        $caption = $input['caption'];
                    }
                }else{
                    $caption = $request->file('file')->getClientOriginalName();
                    $sendData['filename'] = $caption;
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
                $totalStorage = Session::get('storageSize');
                $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,3);
                if($totalStorage + $extraQuotas < (doubleval($uploadedSize) + $file_size) / 1024){
                    return \TraitsFunc::ErrorMessage(trans('main.storageQuotaError'));
                }

                $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image,'','video');
                if($image == false || $fileName[0] == false){
                    return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                }            
                $bodyData = config('app.BASE_URL').'/public/uploads/'.TENANT_ID.'/chats/'.$fileName;
                $message_type = "video";
                $sendData['filename'] = $fileName;
                $sendData['body'] = $bodyData;
                $whats_message_type = 'video';
                $result = $mainWhatsLoopObj->sendFile($sendData);
            }
        }elseif($input['type'] == 4){

            if ($request->hasFile('file')) {
                $image = $request->file('file');
                if(isset($input['size']) && !empty($input['size'])){
                    $file_size = $input['size'];
                    $file_size = $file_size/(1024 * 1024);
                    $file_size = number_format($file_size,2);
                    $uploadedSize = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
                    $totalStorage = Session::get('storageSize');
                    $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,3);
                    if($totalStorage + $extraQuotas < (doubleval($uploadedSize) + $file_size) / 1024){
                        return \TraitsFunc::ErrorMessage(trans('main.storageQuotaError'));
                    }
                    if($input['file'] == false){
                        return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                    }          
                    
                    $fileName = 'record'.time();
                    $destinationPath = public_path().'/uploads/'.TENANT_ID.'/chats/' . $fileName.'.ogg';
                    
                    $filelocationtmp = $_FILES['file']['tmp_name'];

                    // call ffmpeg script to convert file
                    shell_exec("/home/wloop/public_html/vendor/ffmpeg -y -i ".$filelocationtmp." -c:a libopus -vn -b:a 48k ".$filelocationtmp.".ogg");

                    // rename oga file to original file (overrides original)
                    shell_exec("mv ".$filelocationtmp.".ogg ".$destinationPath);

                    $bodyData = config('app.BASE_URL').'/public/uploads/'.TENANT_ID.'/chats/' . $fileName.'.ogg';
                    $message_type = "sound";
                    $whats_message_type = 'ppt';
                    $sendData['audio'] = $bodyData;
                    $result = $mainWhatsLoopObj->sendPTT($sendData);

                }else{
                    $file_size = $image->getSize();
                    $file_size = $file_size/(1024 * 1024);
                    $file_size = number_format($file_size,2);
                    $uploadedSize = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
                    $totalStorage = Session::get('storageSize');
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
                    $sendData['audio'] = $bodyData;
                    $result = $mainWhatsLoopObj->sendPTT($sendData);
                }
               
            }

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
            $bodyData = $sendData['lat'].':'.$sendData['lng'];
            $caption = $input['address'];
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
                $totalStorage = Session::get('storageSize');
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
        if(isset($result['data']) && isset($result['data']['id'])){
            $checkMessageObj = ChatMessage::where('chatId',$sendData['chatId'])->where('chatName','!=',null)->orderBy('messageNumber','DESC')->first();
            $messageId = $result['data']['id'];
            $lastMessage['status'] = $senderStatus;
            $lastMessage['id'] = $messageId;
            $lastMessage['fromMe'] = 1;
            $lastMessage['chatId'] = $sendData['chatId'];
            $lastMessage['time'] = strtotime(date('Y-m-d H:i:s'));
            $lastMessage['body'] = $bodyData;
            $lastMessage['messageNumber'] = $checkMessageObj != null && $checkMessageObj->messageNumber != null ? $checkMessageObj->messageNumber+1 : 1;
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
            $dialog->last_time = $lastMessage['time'];
            $dialogObj = ChatDialog::getData($dialog);
            broadcast(new SentMessage($domain , $dialogObj ));
            ChatMessage::where('chatId',$sendData['chatId'])->where('fromMe',0)->update(['sending_status' => 3]);
            
            $is_admin = IS_ADMIN;
            $user_id = USER_ID; 
            if(!$is_admin){
                $dialogObj = ChatDialog::getData(ChatDialog::getOne($input['chatId']));
                if(in_array($user_id, $dialogObj->modsArr) || IS_ADMIN){
                    ChatEmpLog::newLog($input['chatId'],3);
                }
            }
        }else{
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }

        $dataList['data'] = ChatMessage::getData($messageObj);
        $dataList['status'] = \TraitsFunc::SuccessMessage("Message Sent Successfully !.");
        return \Response::json((object) $dataList);        
    }

    public function liveChatLogout(){
        $is_admin = IS_ADMIN;
        $user_id = USER_ID;
        if(!$is_admin){
            $lastObj = ChatEmpLog::where('user_id',$user_id)->where('type','!=',3)->orderBy('id','DESC')->first();
            if($lastObj != null && $lastObj->ended == 0 && $lastObj->type == 1){
                $lastObj->ended = 1;
                $lastObj->ended_at = DATE_TIME;
                $lastObj->save();
                ChatEmpLog::newRecord($lastObj->chatId,2,$user_id,date('Y-m-d H:i:s'),1);
            }
        }
        return redirect()->to('/dashboard');
    }

    public function labels(Request $request) {
        $input = \Request::all();
        $dataList = Category::dataList();
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function quickReplies(Request $request) {
        $input = \Request::all();
        $dataList = Reply::dataList();
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function moderators(Request $request) {
        $input = \Request::all();
        $dataList = User::getModerators();
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function contact(Request $request) {
        $input = \Request::all();
        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }   

        $chatObj = ChatDialog::getOne($input['chatId']);
        if($chatObj == null){
            return \TraitsFunc::ErrorMessage("Dialog Is Missing");
        }

        $contactObj = Contact::NotDeleted()->where('phone',str_replace('@c.us', '', $input['chatId']))->first();
        if($contactObj == null){
            $contactObj = new Contact;
            $contactObj->group_id = 1;
            $contactObj->status = 1;
            $contactObj->phone = str_replace('@c.us', '', $input['chatId']);
            $contactObj->save();
        }        

        $contact_details = Contact::getData($contactObj,null,null,true);
        $dataObj = ChatDialog::getData($chatObj,false);
        $dataObj->contact_details = $contact_details;
        $dataList['data'] = $dataObj;
        // $dataList['data']->moderators = !empty($dataList['data']->modsArr)  ? User::dataList(null,$dataList['data']->modsArr,'ar')['data'] : [];
        // $dataList['data']->labels = !empty($dataList['data']->metadata['labels']) ? Category::dataList($dataList['data']->metadata['labels'],null)['data'] : [];
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);      
    }

    public function pinChat(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!IS_ADMIN && !\Helper::checkRules('pin-chat')){
            return \TraitsFunc::ErrorMessage("Please Add (pin-chat) Privilege To User's Group");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }
        
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['liveChatId'] = $input['chatId'];
        $result = $mainWhatsLoopObj->pinChat($data);
        $result = $result->json();
        
        $domain = explode('.', $request->getHost())[0];
        $dialogObj = ChatDialog::where('id',$input['chatId'])->first();
        $dialogObj->is_pinned = 1;
        $dialogObj->save();    

        broadcast(new DialogPinStatus($domain, ChatDialog::getData($dialogObj) , 1 ));
        $dataList['data'] = isset($result['data']) ? $result['data'] : '';
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);        
    }

    public function unpinChat(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!IS_ADMIN && !\Helper::checkRules('unpin-chat')){
            return \TraitsFunc::ErrorMessage("Please Add (unpin-chat) Privilege To User's Group");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        $dialogObj = ChatDialog::where('id',$input['chatId'])->first();

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['liveChatId'] = $input['chatId'];
        $result = $mainWhatsLoopObj->unpinChat($data);
        $result = $result->json();
        
        $domain = explode('.', $request->getHost())[0];
        $dialogObj->is_pinned = 0;
        $dialogObj->save();

        broadcast(new DialogPinStatus($domain, ChatDialog::getData($dialogObj) , 0 ));
        $dataList['data'] = isset($result['data']) ? $result['data'] : '';
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);
    }
    
    public function readChat(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!IS_ADMIN && !\Helper::checkRules('read-chat')){
            return \TraitsFunc::ErrorMessage("Please Add (read-chat) Privilege To User's Group");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        $domain = explode('.', $request->getHost())[0];
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['liveChatId'] = $input['chatId'];
        if($domain != 't1365'){
            $result = $mainWhatsLoopObj->readChat($data);
            $result = $result->json();   
        }else{
            return \TraitsFunc::ErrorMessage("Disabled");
        }

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }
        $dialogObj = ChatDialog::where('id',$input['chatId'])->first();
        $dialogObj->is_read = 1;
        $dialogObj->save();

        ChatMessage::where('chatId',$input['chatId'])->update(['sending_status' => 3]);
        broadcast(new ChatReadStatus($domain, ChatDialog::getData($dialogObj) , 1 ));
        
        $dataList['data'] = isset($result['data']) ? $result['data'] : '';
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);   
    }

    public function unreadChat(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!IS_ADMIN && !\Helper::checkRules('unread-chat')){
            return \TraitsFunc::ErrorMessage("Please Add (unread-chat) Privilege To User's Group");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['liveChatId'] = $input['chatId'];
        $result = $mainWhatsLoopObj->unreadChat($data);
        $result = $result->json();

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }
        
        $domain = explode('.', $request->getHost())[0];
        $dialogObj = ChatDialog::where('id',$input['chatId'])->first();
        $dialogObj->is_read = 0;
        $dialogObj->save();

        ChatMessage::where('chatId',$input['chatId'])->update(['sending_status' => 2]);
        broadcast(new ChatReadStatus($domain, ChatDialog::getData($dialogObj) , 0 ));

        $dataList['data'] = isset($result['data']) ? $result['data'] : '';
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);     
    }

    public function labelChat(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!IS_ADMIN && !\Helper::checkRules('label-chat')){
            return \TraitsFunc::ErrorMessage("Please Add (label-chat) Privilege To User's Group");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        if(!isset($input['labelId']) || empty($input['labelId']) ){
            return \TraitsFunc::ErrorMessage("Label ID Is Required");
        }

        if(!isset($input['labelId']) || empty($input['labelId']) ){
            return \TraitsFunc::ErrorMessage("Label ID Is Required");
        }

        $categoryObj = Category::NotDeleted()->where('labelId',$input['labelId'])->first();
        if(!$categoryObj){
            return \TraitsFunc::ErrorMessage("Label Not Found");
        }

        $data['liveChatId'] = $input['chatId'];
        $data['labelId'] = $input['labelId'];
        
        $varObj = Variable::getVar('BUSINESS');
        if($varObj){
            $mainWhatsLoopObj = new \MainWhatsLoop();
            $result = $mainWhatsLoopObj->labelChat($data);
            $result = $result->json();

            if($result['status']['status'] != 1){
                return \TraitsFunc::ErrorMessage($result['status']['message']);
            }    
        }
        
        // dd($input);
        $contactLabelObj = ContactLabel::newRecord(str_replace('@c.us', '', $input['chatId']),$input['labelId']);
       
        $domain = explode('.', $request->getHost())[0];
        broadcast(new ChatLabelStatus($domain, ChatDialog::getData(ChatDialog::getOne($input['chatId'])) , Category::getData($categoryObj) , 1 ));
        $dataList['data'] = $result['data'];
        $dataList['status'] =  $result['status'];
        return \Response::json((object) $dataList);  
    }

    public function unlabelChat(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!IS_ADMIN && !\Helper::checkRules('unlabel-chat')){
            return \TraitsFunc::ErrorMessage("Please Add (unlabel-chat) Privilege To User's Group");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }

        if(!isset($input['labelId']) || empty($input['labelId']) ){
            return \TraitsFunc::ErrorMessage("Label ID Is Required");
        }

        $categoryObj = Category::NotDeleted()->where('labelId',$input['labelId'])->first();
        if(!$categoryObj){
            return \TraitsFunc::ErrorMessage("Label Not Found");
        }

        $data['liveChatId'] = $input['chatId'];
        $data['labelId'] = $input['labelId'];
       
        $varObj = Variable::getVar('BUSINESS'); 
        if($varObj){
            $mainWhatsLoopObj = new \MainWhatsLoop();
            $result = $mainWhatsLoopObj->unlabelChat($data);
            $result = $result->json();
            if($result['status']['status'] != 1){
                return \TraitsFunc::ErrorMessage($result['status']['message']);
            }
        }


        ContactLabel::where('contact',str_replace('@c.us', '', $input['chatId']))->where('category_id',$input['labelId'])->delete();
        $domain = explode('.', $request->getHost())[0];
        broadcast(new ChatLabelStatus($domain, ChatDialog::getData(ChatDialog::getOne($input['chatId'])) , Category::getData($categoryObj) , 0 ));
        $dataList['data'] = $result['data'];
        $dataList['status'] =  $result['status'];
        return \Response::json((object) $dataList);    
    }

    public function updateContact(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!IS_ADMIN && !\Helper::checkRules('update-contact-details')){
            return \TraitsFunc::ErrorMessage("Please Add (update-contact-details) Privilege To User's Group");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }
        
        $contactObj =  Contact::NotDeleted()->where('phone',str_replace('@c.us', '', $input['chatId']))->first();
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
        
        $dataList['status'] = \TraitsFunc::SuccessMessage('Data Updated Successfully.');
        return \Response::json((object) $dataList);      
    }

    

    public function assignMod(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!IS_ADMIN && !\Helper::checkRules('assign-moderator')){
            return \TraitsFunc::ErrorMessage("Please Add (assign-moderator) Privilege To User's Group");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }
        if(!isset($input['modId']) || empty($input['modId']) ){
            return \TraitsFunc::ErrorMessage("Moderator ID Is Required");
        }

        $modObj = User::getOne($input['modId']);
        if($modObj == null){
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
        $dataList['status'] = \TraitsFunc::SuccessMessage("Moderator Added To Conversation Successfully.");
        return \Response::json((object) $dataList);        
    }

    public function removeMod(Request $request) {
        $input = \Request::all();

        if($this->checkPerm()){
            return \TraitsFunc::ErrorMessage('Please Re-activate LiveChat Addon');
        }

        if(!IS_ADMIN && !\Helper::checkRules('remove-moderator')){
            return \TraitsFunc::ErrorMessage("Please Add (remove-moderator) Privilege To User's Group");
        }

        if(!isset($input['chatId']) || empty($input['chatId']) ){
            return \TraitsFunc::ErrorMessage("Chat ID Is Required");
        }
        if(!isset($input['modId']) || empty($input['modId']) ){
            return \TraitsFunc::ErrorMessage("Moderator ID Is Required");
        }

        $modObj = User::getOne($input['modId']);
        if($modObj == null){
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
        $dataList['status'] = \TraitsFunc::SuccessMessage("Moderator Removed From Conversation Successfully.");
        return \Response::json((object) $dataList);        
    }
}
