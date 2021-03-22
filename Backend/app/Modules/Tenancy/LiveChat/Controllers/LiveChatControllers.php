<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\ChatMessage;


class LiveChatControllers extends Controller {

    use \TraitsFunc;

    public function index(Request $request) {
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
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
        }

        $dataList['data'] = $dials;
        $dataList['status'] = \TraitsFunc::SuccessResponse();
        // dd($dataList);
        return \Response::json((object) $dataList);        
    }

    public function sendMessage(Request $request) {
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
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
        }

        $dataList['data'] = $dials;
        $dataList['status'] = \TraitsFunc::SuccessResponse();
        // dd($dataList);
        return \Response::json((object) $dataList);        
    }

}
