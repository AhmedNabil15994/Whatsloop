<?php

use App\Models\UserChannels;
use App\Models\CentralChannel;

class MainWhatsLoop {
    use \TraitsFunc;

    protected $instanceId = "", $token = "",$baseUrl = "";

    public function __construct($instanceId=null,$token=null) {

        $myInstanceId = '';
        $myInstanceToken = '';
        if($instanceId != null && $token != null){
            $channelObj = CentralChannel::where('id',$instanceId)->orWhere('instanceId',$instanceId)->first();
            if($channelObj){
                $myInstanceToken =  $channelObj->instanceToken;
                $myInstanceId = $channelObj->instanceId;
            }
        }else{
            $channelObj = UserChannels::NotDeleted()->where('start_date','<=',date('Y-m-d'))->where('end_date','>=',date('Y-m-d'))->orderBy('id','DESC')->first();
            if($channelObj){
                $channelObj = CentralChannel::NotDeleted()->where('id',$channelObj->id)->first();
                $myInstanceToken =  $channelObj->instanceToken;
                $myInstanceId = $channelObj->instanceId;
            }
        }

        $this->instanceId = $myInstanceId;
        $this->token = $myInstanceToken;
        $this->baseUrl = 'http://engine.whatsloop.loc/';
        // $this->baseUrl = 'http://wloop.net/engine/';
    }

    /*----------------------------------------------------------
    Messages
    ----------------------------------------------------------*/

    //['body' => messageText,'chatId/phone' => 201234123123@c.us For Private MEssages and 20124123123-1231313123@g.us For Group Messages]
    public function sendMessage($data){
        $mainURL = $this->baseUrl.'messages/sendMessage';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['body' => fileUrl,'filename'=> filename,'caption'=> caption,'chatId' => 201234123123@c.us]
    public function sendFile($data){
        $mainURL = $this->baseUrl.'messages/sendFile';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['audio' => fileUrl,'chatId' => 201234123123@c.us]
    public function sendPTT($data){
        $mainURL = $this->baseUrl.'messages/sendPTT';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['body' => link,'title'=> link_title,'description'=> link_description,'previewBase64'=> link_imageAsBase64,'chatId' => 201234123123@c.us]
    public function sendLink($data){
        $mainURL = $this->baseUrl.'messages/sendLink';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['contactId' => contact like 201234123123@c.us ,'chatId' => 201234123123@c.us]
    public function sendContact($data){
        $mainURL = $this->baseUrl.'messages/sendContact';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['lat' => latitude ,'lng'=> longitude,'address'=> addressAsText,'chatId' => 201234123123@c.us]]
    public function sendLocation($data){
        $mainURL = $this->baseUrl.'messages/sendLocation';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['vcard' => vcard v3 format ,'chatId' => 201234123123@c.us]
    public function sendVCard($data){
        $mainURL = $this->baseUrl.'messages/sendVCard';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['messageId' => [false_6590996758@c.us_3EB03104D2B84CEAD82F],'chatId' => 201234123123@c.us]
    public function forwardMessage($data){
        $mainURL = $this->baseUrl.'messages/forwardMessage';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId' => chatId , 'limit' => limit number ]
    public function messages($data){
        $mainURL = $this->baseUrl.'messages/allMessages';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId' => chatId , 'page' => page number , 'count' => count limit  ]
    public function messagesHistory($data){
        $mainURL = $this->baseUrl.'messages/messagesHistory';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['messageId' =>  messageId]
    public function deleteMessage(){
        $mainURL = $this->baseUrl.'messages/deleteMessage';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    /*----------------------------------------------------------
    Instances
    ----------------------------------------------------------*/

    public function status($data=[]){
        $mainURL = $this->baseUrl.'instances/status';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function qr_code($data=[]){
        $mainURL = $this->baseUrl.'instances/qr_code';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function screenshot($data=[]){
        $mainURL = $this->baseUrl.'instances/screenshot';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function logout($data=[]){
        $mainURL = $this->baseUrl.'instances/logout';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function takeover($data=[]){
        $mainURL = $this->baseUrl.'instances/takeover';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function expiry($data=[]){
        $mainURL = $this->baseUrl.'instances/expiry';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function retry($data=[]){
        $mainURL = $this->baseUrl.'instances/retry';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function reboot($data=[]){
        $mainURL = $this->baseUrl.'instances/reboot';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function settings($data){
        $mainURL = $this->baseUrl.'instances/settings';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // $data like Properties that return from settings Get Route (Previous Function)
    public function postSettings($data){
        $mainURL = $this->baseUrl.'instances/updateSettings';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function outputIP(){
        $mainURL = $this->baseUrl.'instances/outputIP';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL);
    }

    public function me(){
        $mainURL = $this->baseUrl.'instances/me';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL);
    }

    // ['name' => new Name]
    public function setName($data){
        $mainURL = $this->baseUrl.'instances/updateName';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['status' => new Status]
    public function setStatus($data){
        $mainURL = $this->baseUrl.'instances/updateStatus';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['messageId' =>  messageId]
    public function repeatHook($data){
        $mainURL = $this->baseUrl.'instances/repeatHook';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function labelsList(){
        $mainURL = $this->baseUrl.'instances/labelsList';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['name' => label name , 'color' => hexColor]
    public function createLabel($data){
        $mainURL = $this->baseUrl.'instances/createLabel';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['name' => label new name , 'color' => label new color , 'labelId' => labelId]
    public function updateLabel($data){
        $mainURL = $this->baseUrl.'instances/updateLabel';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['labelId' => labelId]
    public function removeLabel($data){
        $mainURL = $this->baseUrl.'instances/removeLabel';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }


    /*----------------------------------------------------------
    Dialogs
    ----------------------------------------------------------*/

    // ['limit' => limit count]
    public function dialogs($data){
        $mainURL = $this->baseUrl.'dialogs/allDialogs';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // [ 'chatId' => chatId ]
    public function dialog($data){
        $mainURL = $this->baseUrl.'dialogs/dialog';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['groupName' => group Name,'chatIds' => [chatIds] ]
    public function group($data){
        $mainURL = $this->baseUrl.'dialogs/group';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId' =>  [chatId] ]
    public function pinChat($data){
        $mainURL = $this->baseUrl.'dialogs/pinChat';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId' =>  [chatId] ]
    public function unpinChat($data){
        $mainURL = $this->baseUrl.'dialogs/unpinChat';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId' =>  [chatId] ]
    public function readChat($data){
        $mainURL = $this->baseUrl.'dialogs/readChat';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId' =>  [chatId] ]
    public function unreadChat($data){
        $mainURL = $this->baseUrl.'dialogs/unreadChat';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId' => chatId ]
    public function removeChat($data){
        $mainURL = $this->baseUrl.'dialogs/removeChat';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['code' => group code ]
    public function joinGroup($data){
        $mainURL = $this->baseUrl.'dialogs/joinGroup';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId' => chatId ]
    public function leaveGroup($data){
        $mainURL = $this->baseUrl.'dialogs/leaveGroup';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['groupId'=> groupId like 20124123123-1231313123@g.us,'participantChatId'=> chatId]
    public function addGroupParticipant($data){
        $mainURL = $this->baseUrl.'dialogs/addGroupParticipant';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['groupId'=> groupId like 20124123123-1231313123@g.us,'participantChatId'=> chatId]
    public function removeGroupParticipant($data){
        $mainURL = $this->baseUrl.'dialogs/removeGroupParticipant';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['groupId'=> groupId like 20124123123-1231313123@g.us,'participantChatId'=> chatId]
    public function promoteGroupParticipant($data){
        $mainURL = $this->baseUrl.'dialogs/promoteGroupParticipant';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['groupId'=> groupId like 20124123123-1231313123@g.us,'participantChatId'=> chatId]
    public function demoteGroupParticipant($data){
        $mainURL = $this->baseUrl.'dialogs/demoteGroupParticipant';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId'=> chatId ,'on' => start / stop , 'duration' => number of seconds default 5 ]
    public function typing($data){
        $mainURL = $this->baseUrl.'dialogs/typing';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId'=> chatId ,'on' => start / stop , 'duration' => number of seconds default 5 ]
    public function recording($data){
        $mainURL = $this->baseUrl.'dialogs/recording';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId'=> chatId ,'labelId' => labelId ]
    public function labelChat($data){
        $mainURL = $this->baseUrl.'dialogs/labelChat';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['chatId'=> chatId ,'labelId' => labelId ]
    public function unlabelChat($data){
        $mainURL = $this->baseUrl.'dialogs/unlabelChat';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }


    /*----------------------------------------------------------
    Webhooks
    ----------------------------------------------------------*/

    // ['webhookUrl' => webhookUrl]
    public function webhook($data){
        $mainURL = $this->baseUrl.'webhooks/webhook';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }


    /*----------------------------------------------------------
    Queues
    ----------------------------------------------------------*/

    public function showMessagesQueue(){
        $mainURL = $this->baseUrl.'queues/showMessagesQueue';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function clearMessagesQueue($data=[]){
        $mainURL = $this->baseUrl.'queues/clearMessagesQueue';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function showActionsQueue(){
        $mainURL = $this->baseUrl.'queues/showActionsQueue';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function clearActionsQueue($data=[]){
        $mainURL = $this->baseUrl.'queues/clearActionsQueue';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    /*----------------------------------------------------------
    Ban
    ----------------------------------------------------------*/

    public function banSettings(){
        $mainURL = $this->baseUrl.'ban/banSettings';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    //[ 'banPhoneMask' => Regular expression on which bans on numbers will be sent ,
    //	'preBanMessage' => Warning message If it is set, a message will be sent before sending the ban. ,
    //	'set' => Flag indicating that the current request has changed ban settings ]
    public function postBanSettings($data){
        $mainURL = $this->baseUrl.'ban/updateBanSettings';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['phone' => phone Like 201558659412]
    public function banTest($data){
        $mainURL = $this->baseUrl.'ban/banTest';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    /*----------------------------------------------------------
    Testing
    ----------------------------------------------------------*/

    // ['min_time'=> min time Example: 946684800. , 'max_time' => max time Example: 946684800.]
    public function instanceStatuses($data){
        $mainURL = $this->baseUrl.'testing/instanceStatuses';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['msgId' => msgId ]
    public function webhookStatus($data){
        $mainURL = $this->baseUrl.'testing/webhookStatus';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    // ['phone'=> phone]
    public function checkPhone($data){
        $mainURL = $this->baseUrl.'testing/checkPhone';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    /*----------------------------------------------------------
    Users
    ----------------------------------------------------------*/

    // ['chatId' => chatId ,]
    public function userStatus($data){
        $mainURL = $this->baseUrl.'users/userStatus';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    /*----------------------------------------------------------
    Channels
    ----------------------------------------------------------*/

    public function createChannel(){
        $mainURL = $this->baseUrl.'channels/createChannel';
        return Http::post($mainURL);
    }

    public function channels(){
        $mainURL = $this->baseUrl.'channels';
        return Http::post($mainURL);
    }

    public function transferDays($data){
        $mainURL = $this->baseUrl.'channels/transferDays';
        return Http::post($mainURL,$data);
    }

    public function setSettings($channelId,$channelToken,$data){
        $mainURL = $this->baseUrl.'instances/updateSettings';
        return Http::withHeaders([
            'CHANNELID' => $channelToken,
            'CHANNELTOKEN' => $channelId,
        ])->post($mainURL,$data);
    }
}
