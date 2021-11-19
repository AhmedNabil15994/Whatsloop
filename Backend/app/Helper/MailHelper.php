<?php

class MailHelper
{

    static function prepareEmail($data){
        $emailData['firstName'] = $data['name'];
        $emailData['subject'] = $data['subject'];
        $emailData['content'] = $data['content'];
        $emailData['to'] = $data['email'];
        $emailData['template'] = "emailUsers.emailReplied";
        return self::SendMail($emailData);
    }

    static function SendMail($emailData){

        \Mail::send($emailData['template'], $emailData, function ($message) use ($emailData) {

            $fromEmailAddress = 'noreply@wloop.net';
            $fromDisplayName = 'واتس لووب';

            if(isset($emailData['fromEmailAddress'])){
                $fromEmailAddress = $emailData['fromEmailAddress'];
            }

            if(isset($emailData['fromDisplayName'])) {
                $fromDisplayName = $emailData['fromDisplayName'];
            }

            $message->from($fromEmailAddress, $fromDisplayName);

            $message->to($emailData['to'])->subject($emailData['subject']);

        });
    }
}
