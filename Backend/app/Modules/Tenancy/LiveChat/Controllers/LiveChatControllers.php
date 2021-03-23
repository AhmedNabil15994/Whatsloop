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
                $dials[$key]['label'] = Category::getData(Category::getOne($dialog['metadata']['labels'][0]));
            }
        }

        $dataList['data'] = $dials;
        $dataList['status'] = $result['status'];
        return \Response::json((object) $dataList);        
    }

    public function pinChat(Request $request) {
        $input = \Request::all();
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
        $data['chatId'] = $input['phone'];
        $data['limit'] = isset($input['limit']) && !empty($input['limit']) ? $input['limit'] : 30;

        $dataList = ChatMessage::dataList($data['chatId'].'@c.us',$data['limit']);
        $dataList['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $dataList);        
    }


    // public function sendMessage(Request $request) {
    //     $mainWhatsLoopObj = new \MainWhatsLoop();
    //     $data['limit'] = 0;
    //     $result = $mainWhatsLoopObj->dialogs($data);
    //     $result = $result->json();

    //     if($result['status']['status'] != 1){
    //         return \TraitsFunc::ErrorMessage($result['status']['message']);
    //     }


    //     $dialogs = $result['data']['dialogs'];
    //     $dials = [];
    //     foreach ($dialogs as $key => $dialog) {
    //         $dials[$key] = $dialog;
    //         $dials[$key]['lastMessage'] = ChatMessage::getData(ChatMessage::where('chatId',$dialog['id'])->orderBy('time','DESC')->first());
    //     }

    //     $dataList['data'] = $dials;
    //     $dataList['status'] = \TraitsFunc::SuccessResponse();
    //     // dd($dataList);
    //     return \Response::json((object) $dataList);        
    // }

}
