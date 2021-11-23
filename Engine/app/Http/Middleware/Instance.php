<?php namespace App\Http\Middleware;

use Closure;

class Instance
{

    public function handle($request, Closure $next){
        $statusesArr = [
            // Instances
            'status','qr_code','logout','screenshot','takeover','expiry','retry','reboot','settings','updateSettings','outputIP','me','updateName','updateStatus','repeatHook','labelsList','createLabel','updateLabel','removeLabel','clearInstance',

            // Messages
            'sendMessage','sendFile','sendPTT','sendLink','sendContact','sendLocation','sendVCard','forwardMessage','allMessages','messagesHistory','deleteMessage','sendButtons',
            
            // Webhooks
            'webhook',

            // Dialogs
            'allDialogs','dialog','group','pinChat','unpinChat','readChat','unreadChat','archiveChat','unarchiveChat','disappearingChat','clearChat','removeChat','joinGroup','leaveGroup','addGroupParticipant','removeGroupParticipant','promoteGroupParticipant','demoteGroupParticipant','typing','recording','labelChat','unlabelChat',

            // Queues
            'showMessagesQueue','clearMessagesQueue','showActionsQueue','clearActionsQueue',

            // Ban
            'banSettings','updateBanSettings','banTest',

            // Testing
            'instanceStatuses','webhookStatus','checkPhone',

            // Users
            'userStatus',

            // Products
            'getProducts','getProduct','sendProduct','getOrder',


        ];

        if(!in_array($request->segment(2), $statusesArr)){
            return \TraitsFunc::ErrorMessage("Not Found");
        }

        define('STATUS',$request->segment(2));
        return $next($request);
    }
}
