<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Product;

class ChatMessage extends Model{
    use \TraitsFunc;

    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $fillable = ['id','body','fromMe','isForwarded','author','time','chatId','messageNumber','type','message_type','status','senderName','chatName','caption','sending_status'];    
    public $timestamps = false;
    public $incrementing = false;

    static function getOne($id){
        return self::where('id', $id)->first();
    }

    static function dataList($chatId=null,$limit=null) {
        $input = \Request::all();
        $source = self::NotDeleted();
        if(isset($input['message']) && !empty($input['message'])){
            $source->where('body','LIKE','%'.$input['message'].'%')->orWhere('caption','LIKE','%'.$input['message'].'%');
        }
        if($chatId != null){
            $source->where('chatId',$chatId);
        }
        $source->orderBy('time','DESC');
        return self::generateObj($source,$limit);
    }

    static function lastMessages() {
        $source = self::NotDeleted();
        $source->orderBy('time','DESC');
        return self::generateObj($source,50);
    }

    static function generateObj($source,$limit=null){
        if($limit != null){
            $sourceArr = $source->paginate($limit);
        }else{
            $sourceArr = $source->get();
        }
        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }
        $data['data'] = $list;
        if($limit != null){
            $data['pagination'] = \Helper::GeneratePagination($sourceArr);
        }
        return $data;
    }

    static function newMessage($source,$addHours=null){
        $source = (object) $source;
        $dataObj = self::where('id',$source->id)->first();
        if($dataObj == null){
            $dataObj = new  self;
        }
        
        $dataObj->id = $source->id;
        $dataObj->body = isset($source->body) ? $source->body : '';
        $dataObj->fromMe = isset($source->fromMe) ? $source->fromMe : '';
        $dataObj->isForwarded = isset($source->isForwarded) ? $source->isForwarded : '';
        $dataObj->author = isset($source->author) ? $source->author : '';
        $dataObj->time = isset($source->time) ? $addHours ? strtotime($source->time)+7200 : strtotime($source->time)  : '';
        $dataObj->chatId = isset($source->chatId) ? $source->chatId : '';
        $dataObj->messageNumber = isset($source->messageNumber) ? $source->messageNumber : '';
        if(isset($source->status)){
            $dataObj->status = $source->status;
        }
        if(isset($source->type)){
            $dataObj->type = $source->type;
        }
        // $dataObj->type = isset($source->type) ? $source->type : '' ;
        if(isset($source->message_type)){
            $dataObj->message_type = $source->message_type;
        }
        if(isset($source->sending_status)){
            $dataObj->sending_status = $source->sending_status ;
        }


        $dataObj->frontId = isset($source->frontId) ? $source->frontId : '' ;
        $dataObj->senderName = isset($source->senderName) ? $source->senderName : '' ;
        $dataObj->caption = isset($source->caption) ? $source->caption : '' ;
        $dataObj->chatName = isset($source->chatName) ? $source->chatName : '' ;
        $dataObj->quotedMsgBody = isset($source->quotedMsgBody) ? $source->quotedMsgBody : '' ;
        $dataObj->quotedMsgId = isset($source->quotedMsgId) ? $source->quotedMsgId : '' ;
        $dataObj->quotedMsgType = isset($source->quotedMsgType) ? $source->quotedMsgType : '' ;
        $dataObj->save();

        if(isset($source->metadata)){
            $mainWhatsLoopObj = new \MainWhatsLoop();
            if(isset($source->metadata['orderId']) && isset($source->metadata['orderToken'])){
                $data['orderId'] = $source->metadata['orderId'];
                $data['orderToken'] = $source->metadata['orderToken'];
                $result = $mainWhatsLoopObj->getOrder($data);
                $result = $result->json();

                if($result['status']['status'] == 1 && isset($result['data']['orders']) && !empty($result['data']['orders'])  ){
                    $orderObj = Order::getOne($data['orderId']);
                    if(!$orderObj){
                        $orderObj = new Order;
                        $orderObj->status = 1;
                    }

                    $prodsArr = [];
                    foreach($result['data']['orders'][0]['products'] as $oneProduct){
                        $prodsArr[] = $oneProduct['id'];
                        $productObj = Product::getOne($oneProduct['id']);
                        if(!$productObj){
                            $productObj = new Product;
                        }

                        $productObj->product_id =  $oneProduct['id'];
                        $productObj->name =  $oneProduct['name'];
                        $productObj->currency =  $oneProduct['currency'];
                        $productObj->price =  $oneProduct['price'];
                        $productObj->images =  serialize($oneProduct['images']);
                        $productObj->save();
                    }

                    $orderObj->order_id =  $source->metadata['orderId'];
                    $orderObj->subtotal = $result['data']['orders'][0]['subtotal'];
                    $orderObj->tax = $result['data']['orders'][0]['tax'];
                    $orderObj->total = $result['data']['orders'][0]['total'];
                    $orderObj->message_id = $dataObj->id;
                    $orderObj->products = serialize($result['data']['orders'][0]['products']);
                    $orderObj->client_id = $dataObj->author;
                    $orderObj->created_at = $result['data']['orders'][0]['createdAt'];
                    $orderObj->save();
                }
            }
        }
        return $dataObj;
    }

    static function getData($source){
        $dataObj = new \stdClass();
        if($source){
            $quotedMsgObj = null;
            $dates = self::reformDate($source->time);
            $source = (object) $source;
            $dataObj->id = $source->id;
            $dataObj->body = isset($source->body) ? $source->body : '';
            $dataObj->fromMe = isset($source->fromMe) ? $source->fromMe : '';
            $dataObj->isForwarded = isset($source->isForwarded) ? $source->isForwarded : '';
            $dataObj->author = isset($source->author) ? $source->author : '';
            $dataObj->time = isset($source->time) ? $source->time : '';
            $dataObj->created_at_day = isset($source->time) ? $dates[0]  : ''; 
            $dataObj->created_at_time = isset($source->time) ? $dates[1]  : ''; 
            $dataObj->chatId = isset($source->chatId) ? $source->chatId : '';
            $dataObj->chatId2 = isset($source->chatId) ? self::reformChatId($source->chatId) : '';
            $dataObj->messageNumber = isset($source->messageNumber) ? $source->messageNumber : '';
            $dataObj->status = $source->status != null ? $source->status : ($source->status == null && $source->fromMe == 0 ? $source->senderName : '');
            $dataObj->message_type = $source->message_type  == null ? 'text' :  $source->message_type ;
            $dataObj->whatsAppMessageType = $source->type  != null ? $source->type : '';
            $dataObj->senderName = isset($source->senderName) && $source->senderName != null ? $source->senderName : $source->chatName ;
            $dataObj->caption = isset($source->caption) ? $source->caption : '' ;
            $dataObj->chatName = isset($source->chatName) ? $source->chatName : '' ;
            $dataObj->sending_status = $source->sending_status;
            $dataObj->frontId = $source->frontId;
            $dataObj->sending_status_text = self::getSendingStatus($source->sending_status);
            $dataObj->quotedMsgBody = isset($source->quotedMsgBody) ? $source->quotedMsgBody : '' ;
            $dataObj->quotedMsgId = isset($source->quotedMsgId) ? $source->quotedMsgId : '' ;
            $dataObj->quotedMsgType = isset($source->quotedMsgType) ? $source->quotedMsgType : '' ;
            if($dataObj->quotedMsgId != null){
                $dataObj->quotedMsgObj = self::getData(self::getOne($source->quotedMsgId));
            }
            if(in_array($dataObj->whatsAppMessageType , ['document','video','ppt','image'])){
                $dataObj->file_size = self::getPhotoSize($dataObj->body);
            }
            return $dataObj;
        }
    }  

    static function getSendingStatus($status){
        if($status == 0){
            return trans('main.notSent');
        }else if($status == 1){
            return trans('main.sent');
        }else if($status == 2){
            return trans('main.received');
        }else if($status == 3){
            return trans('main.seen');
        }
    }

    static function reformChatId($chatId){
        $chatId = str_replace('@c.us','',$chatId);
        $chatId = str_replace('@g.us','',$chatId);
        return '+'.$chatId;
    }

    static function reformDate($time){
        $diff = (time() - $time ) / (3600 * 24);
        $date = \Carbon\Carbon::parse(date('Y-m-d H:i:s'));
        if(round($diff) == 0){
            return [date('Y-m-d',$time),date('h:i A',$time)];
        }else if($diff>0 && $diff<=1){
            return [trans('main.yesterday'), date('h:i A',$time)];
        }else if($diff > 1 && $diff < 7){
            return [$date->locale(defined(LANGUAGE_PREF) ? LANGUAGE_PREF : 'ar')->dayName,date('h:i A',$time)];
        }else{
            return [date('Y-m-d',$time),date('h:i A',$time)];
        }
    }

    static function getPhotoSize($url){
        if($url == ""){
            return '';
        }
        if(strpos('http',$url) !== false){
            $image = get_headers($url, 1);
            $bytes = $image["Content-Length"];
            $mb = $bytes/(1024 * 1024);
            return number_format($mb,2) . " MB ";
        }
    }
}
