<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Nicolaslopezj\Searchable\SearchableTrait;

class Bot extends Model{

    use \TraitsFunc,SearchableTrait;

    protected $table = 'bots';
    protected $primaryKey = 'id';
    protected $fillable = ['id','channel','message_type','message','reply_type','reply','file_name','https_url','url_title','url_desc','url_image','whatsapp_no','lat','lng','address','webhook_url','status','created_by','created_at'];    
    public $timestamps = false;

    protected $searchable = [
        'columns' => [
            'message' => 255,
        ],
    ];

    static function getPhotoPath($id, $photo,$tenantId=null) {
        return \ImagesHelper::GetImagePath('bots', $id, $photo,false,$tenantId);
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function findBotMessage($langPref,$senderMessage){
        $botObj = self::NotDeleted()->where('status',1)->where('lang',$langPref)->where('message_type',1)->where('message',$senderMessage)->get();
        if(count($botObj) == 0){
            $botObj = self::NotDeleted()->where('status',1)->where('lang',$langPref)->where('message_type',2)->search($senderMessage, null, true)->get();
            if(count($botObj) == 0){
                $botObj = self::NotDeleted()->where('status',1)->where('lang',$langPref)->where('message_type',2)->search($senderMessage, null, true,true)->get();
            }
        }

        // Check If Message Is Emoji
        preg_match_all('([*#0-9](?>\\xEF\\xB8\\x8F)?\\xE2\\x83\\xA3|\\xC2[\\xA9\\xAE]|\\xE2..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?(?>\\xEF\\xB8\\x8F)?|\\xE3(?>\\x80[\\xB0\\xBD]|\\x8A[\\x97\\x99])(?>\\xEF\\xB8\\x8F)?|\\xF0\\x9F(?>[\\x80-\\x86].(?>\\xEF\\xB8\\x8F)?|\\x87.\\xF0\\x9F\\x87.|..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?|(((?<zwj>\\xE2\\x80\\x8D)\\xE2\\x9D\\xA4\\xEF\\xB8\\x8F\k<zwj>\\xF0\\x9F..(\k<zwj>\\xF0\\x9F\\x91.)?|(\\xE2\\x80\\x8D\\xF0\\x9F\\x91.){2,3}))?))', $senderMessage, $emojis);
        $messageLength = strlen($senderMessage);
        if(isset($emojis[0]) && !empty($emojis[0]) && in_array($messageLength, [4,8])){
            foreach ($botObj as $key => $value) {
                if(in_array(strlen($value->message), [4,8]) && json_encode($senderMessage) == json_encode($value->message)){
                    return [$value];
                }
            }
        }else{
            return $botObj;
        }
    }

    static function dataList($status=null) {
        $input = \Request::all();

        $source = self::NotDeleted();
        if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
            $source->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
        }
        if(isset($input['channel']) && !empty($input['channel'])){
            $source->where('channel',$input['channel']);
        }
        if(isset($input['message_type']) && !empty($input['message_type'])){
            $source->where('message_type',$input['message_type']);
        }
        if(isset($input['message']) && !empty($input['message'])){
            $source->where('message',$input['message']);
        }
        if(isset($input['reply_type']) && !empty($input['reply_type'])){
            $source->where('reply_type',$input['reply_type']);
        }
        if($status != null){
            $source->where('status',$status);
        }
        if(isset($input['channel']) && !empty($input['channel'])){
            $source->where('channel',$input['channel']);
        }else if(Session::has('channelCode')){
            $source->where('channel',Session::get('channelCode'));
        }
        $source->orderBy('sort', 'ASC');
        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        $data['data'] = $list;

        return $data;
    }

    static function getData($source,$tenantId=null) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->channel = $source->channel;
        $data->message_type = $source->message_type;
        $data->message_type_text = self::getMessageType($source->message_type);
        $data->message = $source->message;
        $data->reply_type = $source->reply_type;
        $data->reply_type_text = self::getReplyType($source->reply_type);
        $data->reply = $source->reply;
        $data->reply2 = self::formatReply($source->reply);
        $data->https_url = $source->https_url;
        $data->url_title = $source->url_title;
        $data->url_desc = $source->url_desc;
        $data->url_image = $source->url_image;
        $data->photo = $source->url_image != null ? self::getPhotoPath($source->id, $source->url_image,$tenantId) : "";
        $data->photo_name = $source->url_image;
        $data->photo_size = $data->photo != '' ? \ImagesHelper::getPhotoSize($data->photo) : '';
        $data->file = $source->file_name != null ? self::getPhotoPath($source->id, $source->file_name,$tenantId) : "";
        $data->file_name = $source->file_name;
        $data->file_size = $data->file != '' ? \ImagesHelper::getPhotoSize($data->file) : '';
        $data->file_type = $data->file != '' ? \ImagesHelper::checkFileExtension($data->file_name) : '';
        $data->whatsapp_no = $source->whatsapp_no;
        $data->lat = $source->lat;
        $data->lng = $source->lng;
        $data->address = $source->address;
        $data->webhook_url = $source->webhook_url;
        $data->templates = $source->templates != null ? unserialize(@$source->templates) : [];
        $data->status = $source->status;
        $data->lang = $source->lang;
        $data->langText = $source->lang == 0 ? trans('main.arabic') : trans('main.english');
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }  

    static function formatReply($reply){
        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $reply)){
            $reply = preg_replace("/\*([^*]+)\*/", "*$1*", $reply );
            return $reply;
        }
    }

    static function getMessageType($type){
        $text = '';
        if($type == 1){
            $text = trans('main.equal');
        }else{
            $text = trans('main.part');
        }
        return $text;
    }

    static function getReplyType($type){
        $text = '';
        if($type == 1){
            $text = trans('main.text');
        }else if($type == 2){
            $text = trans('main.photoOrFile');
        }else if($type == 3){
            $text = trans('main.video');
        }else if($type == 4){
            $text = trans('main.sound');
        }else if($type == 5){
            $text = trans('main.link');
        }else if($type == 6){
            $text = trans('main.whatsappNos');
        }else if($type == 7){
            $text = trans('main.mapLocation');
        }else if($type == 8){
            $text = trans('main.webhook');
        }
        return $text;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
