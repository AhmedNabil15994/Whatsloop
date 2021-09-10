<?php

use Illuminate\Support\Facades\Http;

/**
 * This Class For Whatsloop Api To Send ( Message - File - Photo - Location - Voice - Contact )
 *
 * @author WhatsLoop.net
 */
class MainWhatsLoop {
    use \TraitsFunc;

    protected $instanceId = "", $token = "",$baseUrl = "";
   
    public function __construct() {
        $this->instanceId = CHANNEL_ID; //$channelObj->id;
        $this->token = CHANNEL_TOKEN; //$channelObj->token;
        $this->baseUrl = 'https://api.chat-api.com/instance';
    }

    /*----------------------------------------------------------
    Messages
    ----------------------------------------------------------*/

    //['body' => messageText,'chatId/phone' => 201234123123@c.us For Private MEssages and 20124123123-1231313123@g.us For Group Messages]
    public function sendMessage($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'sendMessage?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['body' => fileUrl,'filename'=> filename,'caption'=> caption,'chatId' => 201234123123@c.us]
    public function sendFile($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'sendFile?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['audio' => fileUrl,'chatId' => 201234123123@c.us]
    public function sendPTT($data){   
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'sendPTT?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['body' => link,'title'=> link_title,'description'=> link_description,'previewBase64'=> link_imageAsBase64,'chatId' => 201234123123@c.us]
    public function sendLink($data){ 
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'sendLink?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['contactId' => contact like 201234123123@c.us ,'chatId' => 201234123123@c.us]
    public function sendContact($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'sendContact?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['lat' => latitude ,'lng'=> longitude,'address'=> addressAsText,'chatId' => 201234123123@c.us]]
    public function sendLocation($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'sendLocation?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['vcard' => vcard v3 format ,'chatId' => 201234123123@c.us]
    public function sendVCard($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'sendVCard?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['messageId' => [false_6590996758@c.us_3EB03104D2B84CEAD82F],'chatId' => 201234123123@c.us]
    public function forwardMessage($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'forwardMessage?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['chatId' => chatId , 'limit' => limit number ]
    public function allMessages($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'messages';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // ['chatId' => chatId , 'page' => page number , 'count' => count limit  ]
    public function messagesHistory($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'messagesHistory';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // ['messageId' =>  messageId]
    public function deleteMessage($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'deleteMessage?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    /*----------------------------------------------------------
    Instances
    ----------------------------------------------------------*/

    public function status(){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'status';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    public function qr_code(){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'qr_code?token='.$this->token;
        $data['token'] = $this->token;
        $result = Http::get($fullURL);
        return $result;
    }

    public function logout($data=[]){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'logout?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    public function screenshot($data=[]){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'screenshot';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    public function takeover($data=[]){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'takeover?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    public function expiry($data=[]){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'expiry?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    public function retry($data=[]){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'retry?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    public function reboot($data=[]){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'reboot?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    public function settings(){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'settings';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // $data like Properties that return from settings Get Route (Previous Function) 
    public function updateSettings($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'settings?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    public function outputIP(){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'outputIP';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    public function me(){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'me';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // ['name' => new Name]
    public function updateName($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'setName?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['status' => new Status]
    public function updateStatus($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'setStatus?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['messageId' =>  messageId]
    public function repeatHook($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'repeatHook?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    public function labelsList(){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'labelsList';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // ['name' => label name , 'color' => hexColor]
    public function createLabel($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'createLabel?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['name' => label new name , 'color' => label new color , 'labelId' => labelId]
    public function updateLabel($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'updateLabel?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['labelId' => labelId]
    public function removeLabel($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'removeLabel?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }


    /*----------------------------------------------------------
    Dialogs
    ----------------------------------------------------------*/

    // ['limit' => limit count]
    public function allDialogs($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'dialogs';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // [ 'chatId' => chatId ]
    public function dialog($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'dialog';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // ['groupName' => group Name,'chatIds' => [chatIds] ]
    public function group($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'group?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['chatId' =>  [chatId] ]
    public function pinChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'pinChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['chatId' =>  [chatId] ]
    public function unpinChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'unpinChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['chatId' =>  [chatId] ]
    public function readChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'readChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['chatId' =>  [chatId] ]
    public function unreadChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'unreadChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['chatId' => chatId ]
    public function removeChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'removeChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['code' => group code ]
    public function joinGroup($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'joinGroup?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['chatId' => chatId ]
    public function leaveGroup($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'leaveGroup?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['groupId'=> groupId like 20124123123-1231313123@g.us,'participantPhone'=> chatId]
    public function addGroupParticipant($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'addGroupParticipant?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['groupId'=> groupId like 20124123123-1231313123@g.us,'participantPhone'=> chatId]
    public function removeGroupParticipant($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'removeGroupParticipant?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['groupId'=> groupId like 20124123123-1231313123@g.us,'participantPhone'=> chatId]
    public function promoteGroupParticipant($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'promoteGroupParticipant?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['groupId'=> groupId like 20124123123-1231313123@g.us,'participantPhone'=> chatId]
    public function demoteGroupParticipant($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'demoteGroupParticipant?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['chatId'=> chatId ,'on' => start / stop , 'duration' => number of seconds default 5 ]
    public function typing($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'typing?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['chatId'=> chatId ,'on' => start / stop , 'duration' => number of seconds default 5 ]
    public function recording($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'recording?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['chatId'=> chatId ,'labelId' => labelId ]
    public function labelChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'labelChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }

    // ['chatId'=> chatId ,'labelId' => labelId ]
    public function unlabelChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'unlabelChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result;
    }


    /*----------------------------------------------------------
    Webhooks
    ----------------------------------------------------------*/

    // ['webhookUrl' => webhookUrl]
    public function webhook($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'webhook?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }


    /*----------------------------------------------------------
    Queues
    ----------------------------------------------------------*/

    public function showMessagesQueue(){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'showMessagesQueue';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    public function clearMessagesQueue($data=[]){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'clearMessagesQueue?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    public function showActionsQueue(){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'showActionsQueue';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    public function clearActionsQueue($data=[]){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'clearActionsQueue?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    /*----------------------------------------------------------
    Ban
    ----------------------------------------------------------*/

    public function banSettings(){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'banSettings';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    //[ 'banPhoneMask' => Regular expression on which bans on numbers will be sent , 
    //	'preBanMessage' => Warning message If it is set, a message will be sent before sending the ban. , 
    //	'set' => Flag indicating that the current request has changed ban settings ]
    public function updateBanSettings($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'banSettings?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['phone' => phone Like 201558659412]
    public function banTest($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'banTest?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    /*----------------------------------------------------------
    Testing
    ----------------------------------------------------------*/

    // ['min_time'=> min time Example: 946684800. , 'max_time' => max time Example: 946684800.]
    public function instanceStatuses($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'instanceStatuses';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // ['msgId' => msgId ]
    public function webhookStatus($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'webhookStatus';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // ['phone'=> phone]
    public function checkPhone($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'checkPhone';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    /*----------------------------------------------------------
    Users
    ----------------------------------------------------------*/

    // ['chatId' => chatId ,]
    public function userStatus($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'userStatus';
        $data['token'] = $this->token;
        $result = Http::get($fullURL,$data);
        return $result;
    }

    // Last Updates On 3/9/2021

    /*----------------------------------------------------------
    Dialogs
    ----------------------------------------------------------*/

    // ['chatId' => chatId ]
    public function archiveChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'archiveChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['chatId' =>  chatId ]
    public function unarchiveChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'unarchiveChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['chatId' =>  chatId ]
    public function disappearingChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'disappearingChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['chatId' =>  chatId ]
    public function clearChat($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'clearChat?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    /*----------------------------------------------------------
    Products
    ----------------------------------------------------------*/

    // ['businessId' =>  businessId ]
    public function getProducts($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'getProducts?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['businessId' =>  businessId , 'productId' => productId ]
    public function getProduct($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'getProduct?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['productId' =>  productId , 'filename' => productImage , 'body' => HTTP link , 'phone' => phone ]
    public function sendProduct($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'sendProduct?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }

    // ['orderId' =>  orderId , 'orderToken' => orderToken]
    public function getOrder($data){
        $mainURL = $this->baseUrl.$this->instanceId.'/';
        $fullURL = $mainURL.'getOrder?token='.$this->token;
        $result = Http::post($fullURL,$data);
        return $result; 
    }
}