<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Nicolaslopezj\Searchable\SearchableTrait;

class BotPlus extends Model{

    use \TraitsFunc,SearchableTrait;

    protected $table = 'bot_plus';
    protected $primaryKey = 'id';
    protected $fillable = ['id','channel','message_type','message','title','body','footer','buttons','buttonsData','sort','status','created_by','created_at'];    
    public $timestamps = false;

    protected $searchable = [
        'columns' => [
            'message' => 255,
            // 'body' => 255,
        ],
    ];


    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function findBotMessage($langPref,$senderMessage){
        $botObj = self::NotDeleted()->where('status',1)->where('message_type',1)->search($senderMessage)->first();
        if(!$botObj){
            $botObj = self::NotDeleted()->where('status',1)->where('message_type',2)->search($senderMessage, null, true)->first();
        }
        return $botObj;
    }

    static function getMsg($senderMessage){
        $botObj = self::NotDeleted()->where('status',1)->where('body',$senderMessage)->first();
        if($botObj){
            return self::getData($botObj);
        }
        return $botObj;
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
        $data->title = $source->title;
        $data->body = $source->body;
        $data->footer = $source->footer;
        $data->buttons = $source->buttons;
        $data->buttonsData = unserialize($source->buttonsData);
        $data->status = $source->status;
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

    static function newSortIndex(){
        return self::count() + 1;
    }

}
