<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\ChatMessage;
use App\Models\Category;


class LiveChatControllers extends Controller {

    use \TraitsFunc;

    public function index(){
        return view('Tenancy.LiveChat.Views.index');
    }

    public function dialogs(Request $request) {
        $input = \Request::all();
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : 30;
        $data['page'] = isset($input['page']) && !empty($input['page']) ? $input['page'] : 0;
        $result = $mainWhatsLoopObj->dialogs($data);
        $result = $result->json();

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }


        $dialogs = $result['data']['dialogs'];
        $dials = [];
        foreach ($dialogs as $key => $dialog) {
            $dials[$key] = $dialog;
            $dials[$key]['lastMessage'] = ChatMessage::getData(ChatMessage::where('chatId',$dialog['id'])->orderBy('time','DESC')->first());
            if(isset($dialog['metadata']) && isset($dialog['metadata']['labels']) && isset($dialog['metadata']['labels'][0])){
                $dials[$key]['label'] = Category::getOne($dialog['metadata']['labels'][0]) != null ? Category::getData(Category::getOne($dialog['metadata']['labels'][0])) : [];
            }
        }

        $dataList['data'] = $dials;
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);        
    }

    public function pinChat(Request $request) {
        $input = \Request::all();
        if(!isset($input['phone']) || empty($input['phone']) ){
            return \TraitsFunc::ErrorMessage("Phone Field Is Required");
        }
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['chatId'] = $input['phone'];
        $result = $mainWhatsLoopObj->pinChat($data);
        $result = $result->json();

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }

        $dataList['data'] = $result['data'];
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);        
    }

    public function unpinChat(Request $request) {
        $input = \Request::all();
        if(!isset($input['phone']) || empty($input['phone']) ){
            return \TraitsFunc::ErrorMessage("Phone Field Is Required");
        }
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['chatId'] = $input['phone'];
        $result = $mainWhatsLoopObj->unpinChat($data);
        $result = $result->json();

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }

        $dataList['data'] = $result['data'];
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);        
    }

    public function messages(Request $request) {
        $input = \Request::all();
        if(!isset($input['phone']) || empty($input['phone']) ){
            return \TraitsFunc::ErrorMessage("Phone Field Is Required");
        }
        $data['chatId'] = $input['phone'];
        $data['limit'] = isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : 30;

        $dataList = ChatMessage::dataList($data['chatId'].'@c.us',$data['limit']);
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }

    public function readChat(Request $request) {
        $input = \Request::all();
        if(!isset($input['phone']) || empty($input['phone']) ){
            return \TraitsFunc::ErrorMessage("Phone Field Is Required");
        }
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['chatId'] = $input['phone'];
        $result = $mainWhatsLoopObj->readChat($data);
        $result = $result->json();

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }

        $dataList['data'] = $result['data'];
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);        
    }

    public function unreadChat(Request $request) {
        $input = \Request::all();
        if(!isset($input['phone']) || empty($input['phone']) ){
            return \TraitsFunc::ErrorMessage("Phone Field Is Required");
        }
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['chatId'] = $input['phone'];
        $result = $mainWhatsLoopObj->unreadChat($data);
        $result = $result->json();

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }

        $dataList['data'] = $result['data'];
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);        
    }
   
    public function sendMessage(Request $request) {
        $input = \Request::all();
        if(!isset($input['type']) || empty($input['type']) ){
            return \TraitsFunc::ErrorMessage("Type Field Is Required");
        }

        if(!isset($input['phone']) || empty($input['phone']) ){
            return \TraitsFunc::ErrorMessage("Phone Field Is Required");
        }

        $sendData['phone'] = $input['phone'];
        $mainWhatsLoopObj = new \MainWhatsLoop();

        if($input['type'] == 1){
            if(!isset($input['message']) || empty($input['message']) ){
                return \TraitsFunc::ErrorMessage("Message Field Is Required");
            }

            $message_type = 'text';
            $sendData['body'] = $input['message'];
            $bodyData = $input['message'];
            $result = $mainWhatsLoopObj->sendMessage($sendData);
        }elseif($input['type'] == 2){
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                if($image == false || $fileName == false){
                    return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                }            
                $bodyData = config("app.BASE_URL").'/uploads/chats/'.$fileName;
                $message_type = \ImagesHelper::checkExtensionType(substr($bodyData, strrpos($bodyData, '.') + 1));
                $sendData['filename'] = $fileName;
                $sendData['body'] = $bodyData;
                if($message_type == 'photo'){
                    if(isset($input['caption']) && !empty($input['caption']) ){
                        $sendData['caption'] = $input['caption'];
                    }
                }
                $result = $mainWhatsLoopObj->sendFile($sendData);
            }
        }elseif($input['type'] == 3){
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                if($image == false || $fileName == false){
                    return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                }            
                $bodyData = config("app.BASE_URL").'/uploads/chats/'.$fileName;
                $message_type = "video";
                $sendData['filename'] = $fileName;
                $sendData['body'] = $bodyData;
                $result = $mainWhatsLoopObj->sendFile($sendData);
            }
        }elseif($input['type'] == 4){
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                if($image == false || $fileName == false){
                    return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                }            
                $bodyData = config("app.BASE_URL").'/uploads/chats/'.$fileName;
                $message_type = "sound";
                $sendData['audio'] = $bodyData;
                $result = $mainWhatsLoopObj->sendFile($sendData);
            }
            $result = $mainWhatsLoopObj->sendPTT($sendData);
        }elseif($input['type'] == 5){
            if(!isset($input['contact']) || empty($input['contact']) ){
                return \TraitsFunc::ErrorMessage("Contact Field Is Required");
            }

            $message_type = 'contact';
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
                $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                if($image == false || $fileName == false){
                    return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                }            
                $fullUrl = config("app.BASE_URL").'/uploads/chats/'.$fileName;
            }

            $message_type = 'link';
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
            $messageId = $result['data']['id'];
            $lastMessage['status'] = 'APP';
            $lastMessage['id'] = $messageId;
            $lastMessage['chatId'] = $sendData['phone'].'@c.us';
            $lastMessage['time'] = time();
            $lastMessage['body'] = $bodyData;
            $lastMessage['message_type'] = $message_type;
            $messageObj = ChatMessage::newMessage($lastMessage);
        }

        $dataList['data'] = $messageObj;
        $dataList['status'] = \TraitsFunc::SuccessMessage("Message Sent Successfully !.");
        return \Response::json((object) $dataList);        
    }

}
