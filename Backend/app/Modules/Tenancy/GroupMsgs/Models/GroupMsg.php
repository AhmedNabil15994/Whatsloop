<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class GroupMsg extends Model{

    use \TraitsFunc;

    protected $table = 'group_messages';
    protected $primaryKey = 'id';
    protected $fillable = ['id','channel','group_id','messages_count','message_type','later','publish_at','message','file_name','https_url','url_title','url_desc','url_image','whatsapp_no','lat','lng','address','status','created_by','created_at'];    
    public $timestamps = false;

    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('groupMessages', $id, $photo,false);
    }

    public function Group(){
        return $this->belongsTo('App\Models\GroupNumber','group_id');
    }

    public function Creator(){
        return $this->belongsTo('App\Models\User','created_by');
    }

    public function BotPlus(){
        return $this->belongsTo('App\Models\BotPlus','bot_plus_id');
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$id=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->with(['Group','Creator'])->where(function ($query) use ($input) {
                    if (isset($input['group_id']) && !empty($input['group_id'])) {
                        $query->where('group_id', $input['group_id']);
                    } 
                    if (isset($input['channel']) && !empty($input['channel'])) {
                        $query->where('channel', $input['channel']);
                    } 
                    if (isset($input['message_type']) && !empty($input['message_type'])) {
                        $query->where('message_type', $input['message_type']);
                    } 
                    if (isset($input['message']) && !empty($input['message'])) {
                        $query->where('message', 'LIKE', '%' . $input['message'] . '%');
                    } 
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                    if(isset($input['group_id']) && !empty($input['group_id'])){
                        $query->where('group_id',$input['group_id']);
                    }
                });
        if($status != null){
            $source->where('status',$status);
        }
        
        if(isset($input['channel']) && !empty($input['channel'])){
            $source->where('channel',$input['channel']);
        }else if(Session::has('channelCode') && empty($input['group_id'])){
            $source->where('channel',Session::get('channelCode'));
        }

        if($id != null){
            $source->whereNotIn('id',$id);
        }
        $source->orderBy('id','DESC');
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

    static function getData($source) {
        $data = new  \stdClass();
        $counts = self::getCounts($source);
        $data->id = $source->id;
        $data->channel = $source->channel;
        $data->publish_at = $source->publish_at;
        $data->publish_at2 = \Helper::formatDate($source->publish_at,'M d,Y H:i');
        $data->group_id = $source->group_id;
        $data->group = $source->Group != null ? $source->Group->name_ar : '';
        $data->message_type = $source->message_type;
        $data->message_type_text = self::getMessageType($source->message_type);
        $data->message = self::getMessage($source) != null ? self::getMessage($source) : '';
        $data->file_name = $source->file_name;
        $data->https_url = $source->https_url;
        $data->url_title = $source->url_title;
        $data->url_desc = $source->url_desc;
        $data->url_image = $source->url_image != null ? self::getPhotoPath($source->id, $source->url_image) : "";
        $data->contacts_count = $source->contacts_count;
        $data->messages_count = $source->messages_count;
        $data->sent_msgs = $counts[0];
        $data->unsent_msgs = $counts[1];
        $data->viewed_msgs = $counts[2];
        $data->file = $source->file_name != null ? self::getPhotoPath($source->id, $source->file_name) : "";
        $data->file_name = $source->file_name;
        $data->file_size = $data->file != '' ? \ImagesHelper::getPhotoSize($data->file) : '';
        $data->file_type = $data->file != '' ? \ImagesHelper::checkFileExtension($data->file_name) : '';
        $data->sent_type = self::getStatus($data);
        $data->whatsapp_no = $source->whatsapp_no;
        $data->status = $source->status;
        $data->sort = $source->sort;
        $data->later = $source->later;
        $data->bot_plus_id = $source->bot_plus_id;
        $data->creator = str_replace("+", '', $source->Creator->phone);
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }  

    static function getCounts($source){
        $startTime = date('Y-m-d H:i',strtotime($source->publish_at));
        $endTime = date('Y-m-d H:i',strtotime('+60 minutes',strtotime($startTime)));
        $sent = ContactReport::where('group_message_id',$source->id)->where('message_id','!=','')->count();
        $viewed = ContactReport::where('group_message_id',$source->id)->where('status',3)->count();
        $notSent = $source->contacts_count - $sent - ContactReport::where('group_message_id',$source->id)->where('message_id',null)->count();

        return [$sent,$notSent,$viewed];
    }

    static function getStatus($source){
        if($source->publish_at > date('Y-m-d H:i:s') && (isset($source->later) && $source->later == 1) ){
            return trans('main.publishSoon');
        }

        if($source->sent_msgs + $source->unsent_msgs == $source->contacts_count && $source->sent_msgs > 0){
            return trans('main.sent');
        }else{
            return trans('main.inPrgo');
        }
    }

    static function getMessage($source){
        $text = '';
        if($source->message_type == 1 || $source->message_type == 2){
            $text = $source->message;
        }elseif($source->message_type == 4){
            $text = $source->https_url;
        }elseif($source->message_type == 5){
            $text = $source->whatsapp_no;
        }elseif($source->message_type == 6){
            $text = $source->BotPlus!= null ? $source->BotPlus->body : '';
        }
        return $text;
    }

    static function getMessageType($type){
        $text = '';
        if($type == 1){
            $text = trans('main.text');
        }else if($type == 2){
            $text = trans('main.photoOrFile');
        }else if($type == 3){
            $text = trans('main.sound');
        }else if($type == 4){
            $text = trans('main.link');
        }else if($type == 5){
            $text = trans('main.whatsappNos');
        }
        return $text;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
