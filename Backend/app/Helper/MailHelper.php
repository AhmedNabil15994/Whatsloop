<?php

class MailHelper
{

    static function prepareEmail($data,$type=2,$service=null){
        $emailData['firstName'] = $data['name'];
        $emailData['extras'] = isset($data['extras']) && !empty($data['extras']) ? $data['extras'] : [];
        $emailData['subject'] = self::exchangeVars($data['subject'],$data['name'],$emailData['extras']);
        $emailData['content'] = self::exchangeVars($data['content'],$data['name'],$emailData['extras']);
        $emailData['to'] = $data['email'];
        $emailData['template'] = !isset($data['template']) ? "emailUsers.emailReplied" : $data['template'];
        if(isset($data['url']) && !empty($data['url'])){
            $emailData['url'] = $data['url'];
        }
        if($type == 1){
            return self::SendWhatsAppMessage($emailData,$data['phone'],$service);            
        }
        return self::SendMail($emailData);
    }
    //  ORDER_ID - INVOICE_DATE - DUE_DATE - INVOICE_URL - INVOICE_ID - INVOICE_TOTAL - TRANSACTION_ID - PAYMENT_METHOD - INVOICE_STATUS
    //  COMPANY - URL - CODE - EMPLOYEE_NAME - OWNER - PHONE - PASSWORD
    static function exchangeVars($data,$change,$extras=[]){
        $data = str_replace('{CUSTOMER_NAME}', $change, $data);
        if(!empty($extras)){
            if(isset($extras['invoiceObj'])){
                $data = str_replace('{ORDER_ID}', $extras['invoiceObj']->transaction_id, $data);
                $data = str_replace('{INVOICE_DATE}', $extras['invoiceObj']->created_at, $data);
                $data = str_replace('{DUE_DATE}', $extras['invoiceObj']->due_date, $data);
                $data = str_replace('{INVOICE_URL}', '/invoices/view/'.$extras['invoiceObj']->id, $data);
                $data = str_replace('{INVOICE_ID}', $extras['invoiceObj']->id+10000, $data);
                $data = str_replace('{INVOICE_TOTAL}', $extras['invoiceObj']->roTtotal, $data);
                $data = str_replace('{TRANSACTION_ID}', $extras['invoiceObj']->transaction_id, $data);
                $data = str_replace('{PAYMENT_METHOD}', $extras['invoiceObj']->payment_gateaway, $data);
                $data = str_replace('{INVOICE_STATUS}', $extras['invoiceObj']->statusText, $data);
            }
            if(isset($extras['company'])){
                $data = str_replace('{COMPANY}', $extras['company'], $data);
            }
            if(isset($extras['url'])){
                $data = str_replace('{URL}', $extras['url'], $data);
            }
            if(isset($extras['code'])){
                $data = str_replace('{CODE}', $extras['code'], $data);
            }
            if(isset($extras['employee_name'])){
                $data = str_replace('{EMPLOYEE_NAME}', $extras['employee_name'], $data);
                $data = str_replace('{OWNER}', $extras['owner'], $data);
                $data = str_replace('{PHONE}', $extras['phone'], $data);
                $data = str_replace('{PASSWORD}', $extras['password'], $data);
            }
        }
        return $data;
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

    static function SendWhatsAppMessage($emailData,$phone,$service=null){
        if($service != null){
            $channelObj = \DB::connection('main')->table('channels')->where('id','218103')->first();
        }else{
            $channelObj = \DB::connection('main')->table('channels')->first();
        }

        $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $data['body'] = str_replace(' <br> ','\n',$emailData['content']);
        $data['body'] = str_replace('<br>','\n',$data['body']);
        $data['phone'] = str_replace('+','',$phone);
        $test = $whatsLoopObj->sendMessage($data);
        $result = $test->json();
        if($result['status']['status'] != 1){
            return 0;
        }
    }
}
