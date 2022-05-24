<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserChannels;
use App\Jobs\SyncProducts;
use Nicolaslopezj\Searchable\SearchableTrait;

class ChatMessage extends Model{
    use \TraitsFunc,SearchableTrait;

    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $fillable = ['id','body','fromMe','isForwarded','author','time','chatId','messageNumber','type','message_type','status','senderName','chatName','caption','sending_status','deleted_by','deleted_at'];    
    public $timestamps = false;
    public $incrementing = false;

    protected $searchable = [
        'columns' => [
            'body' => 255,
            'author' => 255,
            'chatId' => 255,
            'senderName' => 255,
            'chatName' => 255,
            'caption' => 255,
        ],
    ];

    public function Order(){
        return $this->belongsTo('App\Models\Order','id','message_id');
    }

    static function getOne($id){
        return self::where('id', $id)->first();
    }

    static function dataList($chatId=null,$limit=null,$disDetails=null,$start=null) {
        $input = \Request::all();
        $source = self::where('id','!=',null);
        if(isset($input['message']) && !empty($input['message'])){
            $source->where('body','LIKE','%'.$input['message'].'%')->orWhere('caption','LIKE','%'.$input['message'].'%');
        }
        if(isset($input['id']) && !empty($input['id'])){
            $source->where('id',$input['id'])->orderBy('time','DESC');
        }
        if($start!= null){
            $source->skip($start);
        }
        if($chatId != null){
            $source->where('chatId',$chatId)->orderBy('time','DESC')->orderBy('messageNumber','DESC');
        }else{
            $source->orderBy('time','DESC');
        }
        return self::generateObj($source,$limit,$disDetails);
    }

    static function lastMessages() {
        $source = self::NotDeleted();
        $source->orderBy('time','DESC');
        return self::generateObj($source,10);
    }

    static function generateObj($source,$limit=null,$disDetails=null){
        if($limit != null){
            $sourceArr = $source->paginate($limit);
        }else{
            $sourceArr = $source->get();
        }
        $list = [];

        $mainUser = User::first();
        $channel = UserChannels::first();
        $domain = $mainUser->domain;
        $disabled = UserAddon::getDeactivated($mainUser->id);
        $dis = 0;
        if(in_array(9,$disabled)){
            $dis = 1;
        }

        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value,$dis,$domain,$disDetails,$channel);
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
        $oldBody = '';
        $oldProd = '';
        if($dataObj == null){
            $dataObj = new  self;
        }else{
            $oldBody = $dataObj->body;
            $oldProd = $dataObj->metadata;
        }
        
        $dataObj->id = $source->id;
        $dataObj->body = (isset($source->body) && empty($oldBody)) || isset($source->type) && in_array($source->type,['vcard','location']) ? $source->body : $oldBody;
        $dataObj->fromMe = isset($source->fromMe) ? $source->fromMe : '';
        $dataObj->isForwarded = isset($source->isForwarded) ? $source->isForwarded : '';
        $dataObj->author = isset($source->author) ? $source->author : '';
        $dataObj->time = isset($source->time) ? $addHours ? strtotime($source->time)+10817 : $source->time  : $dataObj->time;
        $dataObj->chatId = isset($source->chatId) ? $source->chatId : '';
        $dataObj->messageNumber = isset($source->messageNumber) ? $source->messageNumber : '';
        if(isset($source->status) && !$dataObj->status){
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
        if( isset($source->metadata) && $source->metadata != 'null' && $oldProd == ''){
            $dataObj->metadata = json_encode($source->metadata) ;
        }
        if(isset($source->metadata) && $source->metadata != 'null' && isset($source->status) && $source->status == "BOT PLUS"){
            $dataObj->metadata = json_encode($source->metadata) ;
        }
        if( isset($source->module_id) && $source->module_id != '' && $source->module_id != null){
            $dataObj->module_id = $source->module_id;
        }
        if( isset($source->module_status) && $source->module_status != '' && $source->module_status != null){
            $dataObj->module_status = $source->module_status;
        }
        if( isset($source->module_order_id) && $source->module_order_id != '' && $source->module_order_id != null){
            $dataObj->module_order_id = $source->module_order_id;
        }
        $dataObj->save();

        return $dataObj;
    }

    static function getData($source,$dis=1,$domain=null,$disDetails=null,$channel=null){
        $dataObj = new \stdClass();
        if($channel == null){
            $channel = UserChannels::first();
        }
        if($source){
            $quotedMsgObj = null;
            $dates = self::reformDate($source->time);
            $source = (object) $source;
            $dataObj->id = $source->id;
            $dataObj->deleted_by = $source->deleted_by;
            $dataObj->deleted_at = $source->deleted_at;
            $dataObj->module_id = $source->module_id;
            $dataObj->module_status = $source->module_status;
            $dataObj->module_order_id = $source->module_order_id;
            $dataObj->body = isset($source->body) ? ( $source->deleted_by != null ? 'رسالة محذوفة أو غير مدعومة' : $source->body) : '';
            $dataObj->fromMe = isset($source->fromMe) ? $source->fromMe : '';
            $dataObj->isForwarded = isset($source->isForwarded) ? $source->isForwarded : '';
            $dataObj->author = isset($source->author) ? $source->author : '';
            $dataObj->time = isset($source->time) ? $source->time : '';
            $dataObj->created_at_day = isset($source->time) ? $dates[0]  : ''; 
            $dataObj->created_at_time = isset($source->time) ? $dates[1]  : ''; 
            $dataObj->chatId = isset($source->chatId) ? $source->chatId : '';
            $dataObj->chatId2 = isset($source->chatId) ? self::reformChatId($source->chatId) : '';
            $dataObj->messageNumber = isset($source->messageNumber) ? $source->messageNumber : '';
            $dataObj->status = self::getSenderStatus($source);
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
            $dataObj->metadata = isset($source->metadata) ? json_decode($source->metadata) : '';
            if($dataObj->quotedMsgId != null){
                $dataObj->quotedMsgObj = self::getData(self::getOne($source->quotedMsgId));
            }
            if(in_array($dataObj->whatsAppMessageType , ['document','video','ptt','image']) && $disDetails == null){
                $dataObj->file_size = self::getPhotoSize($dataObj->body);
                $dataObj->file_name = $dataObj->whatsAppMessageType != 'image' ? ($source->caption != null ? $source->caption : self::getFileName($dataObj->body) ) : self::getFileName($dataObj->body);
                if( doubleval($dataObj->file_size) == 0){
                    $dataObj->body = config('app.BASE_URL').'/engine/public/uploads/messages/'.$channel->id.'/'.$source->id.'/chatFile.'.self::getExtension(self::getFileName($dataObj->body));
                    $dataObj->file_size = self::getPhotoSize($dataObj->body);
                }
            }
            if(isset($dataObj->whatsAppMessageType) && $dataObj->whatsAppMessageType == 'vcard' && $disDetails == null){
                if(strpos($dataObj->body, 'FN:') !== false){
                    $dataObj->contact_name = str_replace('\n','',explode('TEL',explode('FN:',$dataObj->body)[1])[0]);
                    $dataObj->contact_number = @explode(':+',explode(';waid=',$dataObj->body)[1])[0];
                    $dataObj->contact_number = str_replace('END:VCARD','',$dataObj->contact_number);
                    $arr = explode(':',$dataObj->contact_number) ; 
                    if(isset($arr[1]) && !empty($arr[1])){
                        $dataObj->contact_number = $arr[1];
                    }
                }else{
                    $dataObj->contact_name = $source->body;
                    $dataObj->contact_number = $source->caption;
                }
            }
            if(isset($dataObj->whatsAppMessageType) && $dataObj->whatsAppMessageType == 'order' && $disDetails == null){
                $dataObj->orderDetails = new \stdClass();
                $dataObj->orderDetails->name = $source->Order != null ? (!$dis ? trans('main.order') . ' '.  $source->Order->order_id : $source->caption) : '';
                $dataObj->orderDetails->image = '';
                $dataObj->orderDetails->price = $source->Order != null ? $source->Order->total . ' ' . unserialize($source->Order->products)[0]['currency'] : '';
                $dataObj->orderDetails->quantity = $source->Order !=  null ? $source->Order->products_count : '';
                $dataObj->orderDetails->url = $source->Order != null ? \URL::to('/orders/'.$source->Order->order_id.'/view') : \URL::to('/whatsappOrders/orders');
                $dataObj->orderDetails->url = !$dis ? str_replace('wloop.net',$domain.'.wloop.net',$dataObj->orderDetails->url) : \URL::to('/livechat');
            }
            if(isset($dataObj->whatsAppMessageType) && $dataObj->metadata != '' && $dataObj->whatsAppMessageType == 'product' && $disDetails == null){
                $productObj = Product::getOne($dataObj->metadata->productId);
                if(!$productObj){
                    $productObj = new \stdClass();
                    $productObj->id = 1;
                    $productObj->images = [];
                    $productObj->product_id = $dataObj->metadata->productId;
                    $productObj->name = '';
                    $productObj->currency = ' SAR';
                    $productObj->category_id = 0;
                    $productObj->price = $dataObj->metadata->priceAmount;
                    $productObj->quantity = trans('main.unlimitted');
                    $dataObj->mainImage = ''; 
                }
                $dataObj->productDetails = Product::getData($productObj);

            }
            $dataObj->messageContent = $source->body != null && (strpos(' https',ltrim($source->body)) !== false || strpos(' http',ltrim($source->body)) !== false ) ? 'ðŸ“·' : $source->body;
            if(in_array($dataObj->whatsAppMessageType , ['ptt'])  && $disDetails == true){
                $dataObj->messageContent = trans('main.sound');
            }
            $dataObj->icon = $source->fromMe ? ' <i class="type flaticon-share"></i>' : ' <i class="type color1 flaticon-share"></i>';
            $dataObj->date_time = $dataObj->created_at_day . ' ' . $dataObj->created_at_time;
            $dataObj->chatId3 = str_replace('+','',$dataObj->chatId2);
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

    static function getSenderStatus($source){
        if($source->status != null){
            return $source->status;
        }else{
            if($source->fromMe == 0){
                return $source->senderName;
            }else{
                return 'API';
            }
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
        if(round($diff) == 0 && round($diff) < 1){
            return [trans('main.today'),date('h:i A',$time)];
        }else if($diff>0 && $diff<=1){
            return [trans('main.yesterday'), date('h:i A',$time)];
        }else if($diff > 1 && $diff < 7){
            $myDate = \Carbon\Carbon::parse(date('Y-m-d H:i:s',$time));
            return [$myDate->locale(@defined(LANGUAGE_PREF) ? LANGUAGE_PREF : 'ar')->dayName,date('h:i A',$time)];
        }else{
            return [date('Y-m-d',$time),date('h:i A',$time)];
        }
    }

    static function getPhotoSize($url){
        if($url == ""){
            return '';
        }

        if (filter_var($url, FILTER_VALIDATE_URL)) { 
            $image = @get_headers($url, 1);
            $bytes = @$image["Content-Length"];
            $mb = $bytes/(1024 * 1024);
            return number_format($mb,2) . " MB ";
        }
    }

    static function getFileName($body){
        $names = explode('/',$body);
        return array_reverse($names)[0];
    }
    static function getExtension($body){
        $names = explode('.',$body);
        return array_reverse($names)[0];
    }
}
